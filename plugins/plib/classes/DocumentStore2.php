<?php

/**
 * Copyright (c) Christoph M. Becker
 *
 * This file is part of Plib_XH.
 *
 * Plib_XH is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Plib_XH is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Plib_XH.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Plib;

use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * File system storage of objects implementing `Document2`
 *
 * A minimalist key-value store, where the keys are filenames,
 * and the values are {@see Document2}s.  Different implementations of
 * `Document2` can be maintained in the same `DocumentStore2`.
 *
 * @final
 * @since 1.9
 */
class DocumentStore2
{
    /** @var string */
    private $folder;

    /** @var array<string,array{resource,Document2}> */
    private $open = [];

    public function __construct(string $folder)
    {
        $this->folder = $folder;
    }

    /** @internal */
    public function __destruct()
    {
        assert($this->open === []);
    }

    public function folder(): string
    {
        $this->make(rtrim($this->folder, "/"));
        return $this->folder;
    }

    /**
     * Finds all existing keys recursively
     *
     * @param string $pattern A regular expression to filter the keys
     *
     * @return list<string>
     */
    public function find(string $pattern = ""): array
    {
        $res = [];
        $it = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(
                $this->folder(),
                FilesystemIterator::SKIP_DOTS | FilesystemIterator::UNIX_PATHS
            )
        );
        $it->rewind();
        while ($it->valid()) {
            assert(is_string($it->key()));
            $path = substr($it->key(), strlen($this->folder));
            if ($pattern === "" || preg_match($pattern, $path)) {
                $res[] = $path;
            }
            $it->next();
        }
        return $res;
    }

    /**
     * Creates a new document in the store
     *
     * The `$key` file is created for writing,
     * locked for exclusive access.
     * Returns `null`, if the file already exists; otherwise
     * `$class`'s {@see Document2::createNew()} is called,
     * whose returned value is then returned from this method.
     *
     * Unless the method returns `null`, the file is only
     * unlocked and closed, when {@see DocumentStore2::commit()} or
     * {@see DocumentStore2::rollback()} are called, so it is mandatory to
     * call either method when you are done with the objects.  If you
     * forget that, an assertion will be triggered when the `DocumentStore2`
     * is destroyed.
     *
     * @phpstan-template T of Document2
     * @phpstan-param class-string<T> $class
     * @phpstan-return ?T
     */
    public function create(string $key, string $class): ?Document2
    {
        assert(!isset($this->open[$key]));
        $document = null;
        $dirname = dirname($key);
        if ($dirname !== "" && $dirname !== ".") {
            $this->make($this->folder . $dirname);
        }
        $filename = $this->folder() . $key;
        $stream = @fopen($filename, "x");
        if ($stream === false) {
            return null;
        }
        flock($stream, LOCK_EX);
        $document = $class::new($key);
        $this->open[$key] = [$stream, $document];
        return $document;
    }

    /**
     * Retrieves a document from the store for reading
     *
     * The `$key` file is opened for reading, locked for shared access,
     * its contents are read, and then the file is unlocked and closed.
     * Returns `null` if the file cannot be read; otherwise the contents
     * are passed to `$class`'s {@see Document2::fromString()},
     * whose returned value is then returned from this method.
     *
     * @phpstan-template T of Document2
     * @phpstan-param class-string<T> $class
     * @phpstan-return ?T
     */
    public function read(string $key, string $class): ?Document2
    {
        assert(!isset($this->open[$key]));
        $contents = null;
        $filename = $this->folder() . $key;
        if (is_readable($filename)) {
            if (($stream = fopen($filename, "r")) !== false) {
                flock($stream, LOCK_SH);
                if (($contents = stream_get_contents($stream)) === false) {
                    $contents = null;
                }
                flock($stream, LOCK_UN);
                fclose($stream);
            }
        }
        if ($contents === null) {
            return null;
        }
        return $class::fromString($contents, $key);
    }

    /**
     * Retrieves a document from the store for modification
     *
     * The `$key` file is opened for reading and writing,
     * locked for exclusive access, and its contents are read.
     * Returns `null`, if the file cannot be read; otherwise the contents
     * are passed to `$class`'s {@see Document2::fromString()},
     * whose returned value is then returned from this method.
     *
     * Unless the method returns `null`, the file is only
     * unlocked and closed, when {@see DocumentStore2::commit()} or
     * {@see DocumentStore2::rollback()} are called, so it is mandatory to
     * call either method when you are done with the objects.  If you
     * forget that, an assertion will be triggered when the `DocumentStore2`
     * is destroyed.
     *
     * @phpstan-template T of Document2
     * @phpstan-param class-string<T> $class
     * @phpstan-return ?T
     */
    public function update(string $key, string $class): ?Document2
    {
        assert(!isset($this->open[$key]));
        $contents = null;
        $document = null;
        $dirname = dirname($key);
        if ($dirname !== "" && $dirname !== ".") {
            $this->make($this->folder . $dirname);
        }
        $filename = $this->folder() . $key;
        $stream = @fopen($filename, "r+");
        if ($stream === false) {
            $stream = @fopen($filename, "r");
        }
        if ($stream !== false) {
            flock($stream, LOCK_EX);
            if (($contents = stream_get_contents($stream)) === false) {
                $contents = null;
            }
        }
        if ($contents !== null) {
            $document = $class::fromString($contents, $key);
        }
        if ($stream) {
            if ($contents === null || $document === null) {
                flock($stream, LOCK_UN);
                fclose($stream);
            } else {
                $this->open[$key] = [$stream, $document];
            }
        }
        return $document;
    }

    /**
     * Commits all pending changes to the store
     */
    public function commit(): bool
    {
        foreach (array_reverse($this->open) as $key => [$stream, $document]) {
            $contents = $document->toString();
            if ($contents !== null) {
                rewind($stream);
                if (($length = @fwrite($stream, $contents)) !== strlen($contents)) {
                    $this->rollback();
                    return false;
                }
                ftruncate($stream, $length);
            }
            flock($stream, LOCK_UN);
            fclose($stream);
            unset($this->open[$key]);
            if ($contents === null) {
                return false;
            }
        }
        return true;
    }

    /**
     * Rolls back all pending changes to the store
     */
    public function rollback(): void
    {
        foreach ($this->open as $key => [$stream, ]) {
            flock($stream, LOCK_UN);
            fclose($stream);
            unset($this->open[$key]);
        }
    }

    /**
     * Deletes a key from the store
     */
    public function delete(string $key): bool
    {
        $filename = $this->folder() . $key;
        if (!is_file($filename)) {
            return true;
        }
        return unlink($filename);
    }

    private function make(string $foldername): void
    {
        if (!is_dir($foldername)) {
            mkdir($foldername, 0777, true);
            chmod($foldername, 0777);
        }
    }
}
