# dokuwiki-evefit-plugin
EVE Fitting formatting plugin for DokuWiki

## Usage
Adds wiki syntax for EVE Online[1] ship fittings.  An EFT-formatted[2] fit between  `[Fit][/Fit]` tags will be rendered as a summary line with attributes and an o.smium.org[3] link followed by an expandable listing of the fit:

```
[Fit]
[Drake, C4 Spider Drake]
Power Diagnostic System II
Missile Guidance Enhancer II
Ballistic Control System II
Ballistic Control System II

Phased Weapon Navigation Array Generation Extron
Adaptive Invulnerability Field II
Adaptive Invulnerability Field II
EM Ward Amplifier II
Cap Recharger II
Large Shield Extender II

Gistum C-Type Medium Remote Shield Booster
'Arbalest' Heavy Missile Launcher, Scourge Heavy Missile
'Arbalest' Heavy Missile Launcher, Scourge Heavy Missile
'Arbalest' Heavy Missile Launcher, Scourge Heavy Missile
'Arbalest' Heavy Missile Launcher, Scourge Heavy Missile
'Arbalest' Heavy Missile Launcher, Scourge Heavy Missile
'Arbalest' Heavy Missile Launcher, Scourge Heavy Missile

Medium Capacitor Control Circuit I
Medium Capacitor Control Circuit I
Medium Capacitor Control Circuit I


Warrior II x5
[/Fit]
```

## Installation
Copy the plugin to your `dokuwiki/lib/plugins` directory.

## Development

* Install PHP with libcurl and libjson
* Download dokuwiki[4] and symlink the `evefit` directory to the `lib/plugins` directory
* `php -S localhost:1234` in the dokuwiki root directory
* Submit pull request


[1]: http://www.eveonline.com/
[2]: https://forums.eveonline.com/default.aspx?g=posts&t=24359&find=unread
[3]: https://o.smium.org/
[4]: http://dokuwiki.org/