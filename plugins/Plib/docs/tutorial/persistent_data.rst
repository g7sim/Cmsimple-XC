Persistent Data
===============

Plugins often have the need to store their own data,
and that is typically done in flat text files.
Let us have a look at how this is done in the Online plugin:

.. code-block:: php
    $tmpdata = $pth['folder']['plugins'].$plugin."/data/online.txt";

    if(!is_file($tmpdata)) {
        $handle = fopen($tmpdata, "w");
        fclose($handle);
        chmod($tmpdata,0666);
        }
        
        $f = fopen($tmpdata, "r+");
        flock($f,2);
        
    while (!feof($f))
        {
        $user[] = chop(fgets($f,65536));
        }
    fseek($f,0,SEEK_SET);
    ftruncate($f,0);
    
    foreach ($user as $line)
        {
        list($savedip,$savedtime) = split("\|",$line);
        if ($savedip == $ip) {$savedtime = $time;$found = 1;}
        if ($time < $savedtime + ($minutes * 60)) 
            {
            fputs($f,"$savedip|$savedtime\n");
            $users = $users + 1;
            }
        }
    
    if ($found == 0) 
        {
        fputs($f,"$ip|$time\n");
        $users = $users + 1;
        }
        
    fclose ($f);    

Although the logic is not really complex, the code is hard to
understand, and obiously hard to test, because it mixes the concerns
of the file storage, and the logic on how to maintain the data.
Can we do better?  Let us have a look at the `Document` interface
and `DocumentStore` class.

First, we create a suitable class which implements the `Document`
methods:

.. code-block:: php
    class Online implements Document
    {
        private $times = [];

        public static function fromString(string $contents, string $key)
        {
            $that = new static();
            $lines = explode("\n", $contents);
            foreach ($lines as $line) {
                [$ip, $time] = explode("|", $line);
                $that->times[$ip] = (int) $time;
            }
            return $that;
        }

        public function toString(): string
        {
            $lines = [];
            foreach ($this->times as $ip => $time) {
                $lines[] = $ip . "|" . $time;
            }
            return implode("\n", $lines);
        }
    }

`Online::fromString()` parses the `$contents`, and stores the relevant
data in `Online::$times`, while `Online::toString()` reassembles
the string representation from the private property.
Next, we implement the three pieces of logic, namely to 
update the timestamp for the current `$ip`, to
delete all entries older than `$minutes * 60` seconds,
and to get the number of users who are currently online.

.. code-block:: php
    class Online implements Document
    {
        // existing code omitted for brevity

        public function updateTimestamp(string $ip, int $time): void
        {
            $this->times[$ip] = $time;
        }

        public function removeOfflineUsers(int $now): void
        {
            $this->times = array_filter($this->times, function (int $time) use ($now) {
                return $now < $time + ($minutes * 60);
            });
        }

        public function countOnlineUsers(): int
        {
            return count($this->times);
        }
    }

Finally, we assemble that in the `gonline()` function:

.. code-block:: php
    $store = new DocumentStore($pth["folder"]["plugins"] . "online/data/");
    $model = $store->update("online.txt", Online::class);
    $model->updateTimestamp($ip, $time);
    $model->removeOfflineUsers($time);
    $store->commit(); // save right away, we are not doing further modifications
    $users = $model->countOnlineUsers();

While this is obviously more code than in the original (although not
much, because the actual file access code is provided by `DocumentStore`),
it is much easier to understand and maintain.

The toplevel code is crystal clear: get the model, update the timestamp
of the current user, remove the offline users, save, and finally count
the remaining online users.  Even no need for some additional comments.
And if we wanted to be a bit more robust than in the original,
because writing to a file can always fail for various reasons,
we only had to check the return value of `$store->commit()`
instead of checking multiple lowlevel filesystem calls (such as `fputs`).

The implementation of the `Online` class is also easy to understand,
and if we wanted to change the data format, we would only have to
update the `::fromString()` and `::toString()` methods – nothing else.

And we can test the `Online` class without further ado:
just call `Online::fromString()` with some string fixture,
then call a method, and check whether `Online::toString()`
returns the desired string.

Looking back at the `gonline_internal()` function of the previous section,
we could also pass the `DocumentStore` instead of creating that inside
the function; then we could easily test the whole `gonline_internal()`
function with a broad test, by passing in a `DocumentStore` mock.
