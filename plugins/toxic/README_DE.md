# Toxic_XH

Toxic_XH ermöglicht die Verwendung von erweiterten TOCs (engl. Table of Contents;
Inhaltsverzeichnissen), die Features bieten, die nicht von den eingebauten
`toc()` und `li()` Funktionen von CMSimple_XH unterstützt werden, sondern
vergleichbar mit denen sind, die von den alten AdvancedTOC und xtoc Add-Ons
angeboten wurden.

- [Voraussetzungen](#voraussetzungen)
- [Download](#download)
- [Installation](#installation)
- [Einstellungen](#einstellungen)
- [Verwendung](#verwendung)
- [Problembehebung](#problembehebung)
- [Lizenz](#lizenz)
- [Danksagung](#danksagung)

## Voraussetzungen

Toxic_XH ist ein Plugin für [CMSimple_XH](https://www.cmsimple-xh.org/de/).
Es benötigt CMSimple_XH ≥ 1.7.0 und PHP ≥ 7.1.0.
Toxic_XH benötigt weiterhin [Plib_XH](https://github.com/cmb69/plib_xh) ≥ 1.8;
ist dieses noch nicht installiert (siehe `Einstellungen` → `Info`),
laden Sie das [aktuelle Release](https://github.com/cmb69/plib_xh/releases/latest)
herunter, und installieren Sie es.

## Download

Das [aktuelle Release](https://github.com/cmb69/toxic_xh/releases/latest)
kann von Github herunter geladen werden.

## Installation

Die Installation erfolgt wie bei vielen anderen CMSimple_XH-Plugins auch.

1. Sichern Sie die Daten auf Ihrem Server.
1. Entpacken Sie die ZIP-Datei auf Ihrem Rechner.
1. Laden Sie das ganze Verzeichnis `toxic/` auf Ihren Server
   in das `plugins/` Verzeichnis von CMSimple_XH hoch.
1. Machen Sie die Unterverzeichnisse `css/`
   und `languages/` beschreibbar.
1. Navigieren Sie zu `Plugins` → `Toxic` im Administrationsbereich,
   und prüfen Sie, ob alle Voraussetzungen erfüllt sind.

## Einstellungen

Die Plugin-Konfiguration erfolgt wie bei vielen anderen
CMSimple_XH-Plugins auch im Administrationsbereich der Website.
Gehen Sie zu `Plugins` → `Toxic`.

Die Voreinstellungen von Toxic_XH können unter `Konfiguration` geändert
werden. Beim Überfahren der Hilfe-Icons mit der Maus werden Hinweise
zu den Einstellungen angezeigt.

Die Lokalisierung wird unter `Sprache` vorgenommen.
Sie können die Zeichenketten in Ihre eigene Sprache übersetzen,
falls keine entsprechende Sprachdatei zur Verfügung steht,
oder sie entsprechend Ihren Anforderungen anpassen.

Das Aussehen von Toxic_XH kann unter `Stylesheet` angepasst werden.

## Verwendung

Toxic_XH funktioniert nur für Templates, die die Standard-Menü-Funktionen
vom CMSimple_XH verwenden; selbsterstellte Funktionen, die von einigen
Templates genutzt werden, werden nicht unterstützt.

Um die erweiterten Toxic_XH Features zu aktivieren, müssen die
Standard-Menü-Funktionen mit den Drop-in-Replacements von Toxic_XH
ersetzt werden, d.h

    <?=toc(…)?>

durch

    <?=toxic(…)?>

und/oder

    <?=li(…)?>

durch

    <?=toxic_li(…)?>

und/oder

    <?=submenu(…)?>

durch

    <?=toxic_submenu(…)?>

Dies aktiviert die Einstellungen im Page-Data-Reiter `Toxic`, die für
individuelle Seiten getroffen werden können.

Es kann eine Kategorie für jede Seite eingegeben werden, was bedeutet, dass
eben diese Seite die erste Seite der Kategorie ist. Die Kategorie selbst wird
als separater Menüpunkt im Menü dargestellt, und hat rein informativen oder
visuallen Charakter, aber ist nicht funktional (beispielsweise ist die Kategorie
nicht verlinkt). Jeder Katergorie-Menüpunkt hat die CSS-Klasses `toxic_category`,
die genutzt werden kann, um den Menüpunkt zu gestalten. Es ist zu beachten,
dass ein Kategorieeintrag beliebiges HTML-Markup akzeptiert, so dass ein Bild
anstatt eines Textes für die Kategorie verwendet werden kann.

Es kann eine individuelle CSS-Klasse für jeden Menüpunkt gewählt werden, die
dann dem entsprechenden Listenpunkt (`<li>`) zugewiesen wird, zusätzlich zu den
Standard-CSS-Klassen `sdoc`/`sdocs`/`doc`/`docs` von CMSimple_XH. Es können
entsprechende Regeln zum Template-Stylesheet hinzugefügt werden, um den
Menüpunkt nach Wunsch zu gestalten.

## Problembehebung

Melden Sie Programmfehler und stellen Sie Supportanfragen entweder auf
[Github](https://github.com/cmb69/toxic_xh/issues)
oder im [CMSimple\_XH Forum](https://cmsimpleforum.com/).

## Lizenz

Toxic_XH ist freie Software. Sie können es unter den Bedingungen
der GNU General Public License, wie von der Free Software Foundation
veröffentlicht, weitergeben und/oder modifizieren, entweder gemäß
Version 3 der Lizenz oder (nach Ihrer Option) jeder späteren Version.

Die Veröffentlichung von Toxic_XH erfolgt in der Hoffnung, daß es
Ihnen von Nutzen sein wird, aber *ohne irgendeine Garantie*, sogar ohne
die implizite Garantie der *Marktreife* oder der *Verwendbarkeit für einen
bestimmten Zweck*. Details finden Sie in der GNU General Public License.

Sie sollten ein Exemplar der GNU General Public License zusammen mit
Toxic_XH erhalten haben. Falls nicht, siehe <https://www.gnu.org/licenses/>.

Copyright 1999-2009 Peter Harteg<br>
Copyright 2014 [The CMSimple_XH developers](https://cmsimple-xh.org/?The_Team)<br>
Copyright © Christoph M. Becker

## Danksagung

Das Plugin-Icon wurde von [new mooon](https://code.google.com/u/newmooon/) gestaltet.
Vielen Dank für die Veröffentlichung unter GPL.

Vielen Dank an die Community im
[CMSimple_XH-Forum](https://www.cmsimpleforum.com/)
für Tipps, Vorschläge und das Testen.
Besonderer Dank geht an *manu*, der durch eine Nachfrage zu `toc()` die
Wiederaufnahme der Arbeit am Plugin ausgelöst hat.

Und zu guter Letzt vielen Dank an
[Peter Harteg](https://www.harteg.dk/), den „Vater“ von CMSimple,
und alle Entwickler von [CMSimple_XH](https://www.cmsimple-xh.org/de/),
ohne die dieses phantastische CMS nicht existieren würde.
