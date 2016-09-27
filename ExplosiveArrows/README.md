ExplosiveArrows
===================
__PocketMine Plugin__

### About

ExplosiveArrows allows players to wreak explosive havoc on your server. You can set the default settings for explosions
in the Settings.yml configuration file, however you should note that these settings can be overridden through the use of
NBT. You can give yourself and other players bows that carry their own explosion settings, this allows you to create
extremely rare lethal weapons. Beware that if a bow overrides the the terrain-damage option one arrow could quite
literally destroy your entire server.

#### So how do I give myself these awesome explosive bows?
Good question! There are two ways you can give yourself a custom bow, both are relatively easy.

__Method #1__

This will give a player a bow using the command within this plugin.
```
/givebow <player> <explosion size> <terrain damage> <custom name>

Example:

/givebow Jack 4 no §l§6Explosive Bow
```
This will give a player named Jack a bow named §l§6Explosive Bow with an explosion size of 4 and will not do terrain
damage.

__Method #2__

This will give a player a bow using the default /give command.
```
/give bow 0 1 <player> "{display:{Name:\"§r<custom name>§r\"},ExplosionSize:<explosion size>i,TerrainDamage:<terrain damage>

Example:

/give bow 0 1 Jack "{display:{Name:\"§r§l§cVERY Explosive Bow§r\"},ExplosionSize:12i,TerrainDamage:1}
```
This will give a player named Jack a bow named §l§cVERY Explosive Bow with an explosion size of 12 and will do terrain
damage.


__The content of this repo is licensed under the GNU Lesser General Public License v2.1. A full copy of the license is
available [here](LICENSE).__
