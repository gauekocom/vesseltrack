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

    class Vessel{
        private $mmsi;
        private $vesselName;
        private $latitude = 0;
        private $longitude = 0;
        private $destination;
        private $lastPosDate;
        private $zoom = 11;
        private $poi = '';
        private $embedCode = '';

        function __construct($mmsi) {
            $this->setMmsi($mmsi);
            $this->genVesselName();
            $this->genLastPosDate();
            $this->genPosition();
            $this->genDestination();
            $this->genPoi();
            $this->genEmbedCode();
        }

        public function setMmsi($mmsi){
            $this->mmsi = $mmsi;
        }

        public function getMmsi(){
            return $this->mmsi;
        }

        public function setLatitude($latitude){
            $this->latitude = $latitude;
        }

        public function getLatitude(){
            return $this->latitude;
        }

        public function setLongitude($longitude){
            $this->longitude = $longitude;
        }

        public function getLongitude(){
            return $this->longitude;
        }

        public function setZoom($zoom){
            $this->zoom = $zoom;
        }

        public function getZoom(){
            return $this->zoom;
        }

        public function setVesselName($vesselName){
            $this->vesselname = $vesselName;
        }

        public function genVesselName(){
            $ch = curl_init('http://www.marinetraffic.com/ais/shipdetails.aspx?mmsi='.$this->mmsi);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
            $content = curl_exec($ch);
            curl_close($ch);

            if (preg_match_all('/(<h1.*>)(\w.*)(<\/h1>)/isxmU', $content, $matches, PREG_SET_ORDER)){
                  $this->vesselName = substr($matches[0][0], 4, -5);;
            }else{
                $this->vesselName = 'Error';
            }
        }

        public function getVesselName(){
            return $this->vesselName;
        }

        public function setDestination($destination){
            $this->destination = $destination;
        }

        public function genDestination(){
            $ch = curl_init('http://www.marinetraffic.com/ais/shipdetails.aspx?mmsi='.$this->mmsi);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
            $content = curl_exec($ch);
            curl_close($ch);

            if (preg_match_all('/<b>Destination:<\/b>.+<br\/><b>ETA:<\/b>/', $content, $matches, PREG_SET_ORDER)){
                  $this->destination = substr($matches[0][0], 20, -16);;
            }else{
                $this->destination = 'Error';
            }

        }

        public function getDestination(){
            return $this->destination;
        }

        public function genPosition(){
            $ch = curl_init('http://www.marinetraffic.com/ais/shipdetails.aspx?mmsi='.$this->mmsi);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
            $content = curl_exec($ch);
            curl_close($ch);


            if(preg_match_all('/(-)?[0-9]{1,3}+\.[0-9]+\&deg;/', $content, $matches, PREG_SET_ORDER)) {
                $this->latitude = substr($matches[0][0], 0, -5);
                $this->longitude = substr($matches[1][0], 0, -5);
            }elseif(preg_match_all('/(-)?[0-9]{1,3}+\.[0-9]+\˚/', $content, $matches, PREG_SET_ORDER)){
                $this->latitude = substr($matches[0][0], 0, -2);
                $this->longitude = substr($matches[1][0], 0, -2);
            }else{
                $this->latitude = 'Error';
                $this->longitude = 'Error';
            }

        }

       public function genLastPosDate(){
            $ch = curl_init('http://www.marinetraffic.com/ais/shipdetails.aspx?mmsi='.$this->mmsi);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
            $content = curl_exec($ch);
            curl_close($ch);

            if (preg_match_all('/Info Received:(.+)ago</', $content, $matches, PREG_SET_ORDER)) {
                  $this->lastPosDate = substr($matches[0][0], 19, -1);
            }else{
                  $this->lastPosDate =  'Error';
            }

        }

        public function getLastPosDate(){
            return $this->lastPosDate;
        }

       public function genPoi(){
           $this->poi = 'lat'."\t".'lon'."\t".'title'."\t".'description'."\t".'icon'."\t".'iconSize'."\t".'iconOffset'."\r\n"
                      . $this->latitude."\t".$this->longitude."\t".$this->vesselName."\t".'Latitude: '.$this->latitude.'<br>Longitude: '.$this->longitude.'<br><br>Destination:<br>'.$this->destination.'<br><br>Last update:<br>'.$this->lastPosDate.'<br><br><small>Data Source: <a href="http://www.marinetraffic.com/">Marine Traffic.com</a>.</small>'."\t".'img/sailboat.png'."\t".'24,24'."\t".'0,-24'."\r\n";

          $handle = fopen('pois.txt', 'w');
          fwrite($handle, $this->poi);
          fclose($handle);
       }

        public function genEmbedCode(){
            $this->embedCode =  '<div id="mapdiv"></div>'."\r\n"
                              . '<script src="http://www.openlayers.org/api/OpenLayers.js"></script>'."\r\n"
                              . '<script>'."\r\n"
                              . '    map = new OpenLayers.Map("mapdiv");'."\r\n"
                              . '    map.addLayer(new OpenLayers.Layer.OSM());'."\r\n"
                              . "\r\n"
                              . '    var pois = new OpenLayers.Layer.Text( "My Points",'."\r\n"
                              . '                     { location:"./pois.txt",'."\r\n"
                              . '                       projection: map.displayProjection'."\r\n"
                              . '                      });'."\r\n"
                              . '    map.addLayer(pois);'."\r\n"
                              . "\r\n"
                              . '    //Set start centrepoint and zoom'."\r\n"
                              . '    var lonLat = new OpenLayers.LonLat( '.$this->longitude.', '.$this->latitude.' )'."\r\n"
                              . '        .transform('."\r\n"
                              . '            new OpenLayers.Projection("EPSG:4326"), // transform from WGS 1984'."\r\n"
                              . '            map.getProjectionObject() // to Spherical Mercator Projection'."\r\n"
                              . '        );'."\r\n"
                              . '    var zoom='.$this->zoom.';'."\r\n"
                              . '    map.setCenter (lonLat, zoom);'."\r\n"  
                              ."\r\n" 
                              . '</script>'."\r\n";
        }

        public function getEmbedCode(){
            return $this->embedCode;
        }

        public function printEmbedCode(){
            print $this->embedCode;
        }

    }
?>