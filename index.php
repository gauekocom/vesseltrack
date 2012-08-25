<?php
/*
All Vessel Track code is Copyright 2012 - 2012 by Gaueko Koop. Elk. Txikia.

This file is part of Vessel Track.

Vessel Track is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
(at your option) any later version.

Vessel Track is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Vessel Track.  If not, see <http://www.gnu.org/licenses/>.
*/
?>

<div class=embed>
    <link rel="stylesheet" href="./css/style.css" type="text/css" media="all" />
        <?php
            include('./classes/Vessel.php');

            if(!empty($_GET['mmsi'])){
                $mmsi = $_GET['mmsi'];
            }else{
                $mmsi = 230314000;
            }

            $estelle = new Vessel($mmsi);
            $estelle->printEmbedCode();
        ?>
</div>