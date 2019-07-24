<?php
declare(strict_types = 1);
namespace GoWest\Sectioncontent\Form\Element;

use TYPO3\CMS\Backend\Form\Element\AbstractFormElement;

class MapPickerElement extends AbstractFormElement
{
    public function render()
    {
        // Custom TCA properties and other data can be found in $this->data, for example the above
        // parameters are available in $this->data['parameterArray']['fieldConf']['config']['parameters']
        $result = $this->initializeResultArray();
        $data = $this->data;
        $data['renderType'] = 'input';
        $childArray = $this->nodeFactory->create($data)->render();
        $resultArray = $this->mergeChildReturnIntoExistingResult($result, $childArray, false);
        $uniqeMapId = 'map-' . uniqid();
        $this->mapsApiKey = '';
        
        $result['html'] = $this->getJavaScript($uniqeMapId) . $childArray['html'] . "
        
        
                <div id=\"" . $uniqeMapId . "\" ></div>


        
        ";
        #$result['html'] = '<input type="text" name="data[pages][5][tx_sectioncontent_abstract_title]" value="" /> my map content';
        return $result;
    }
    
    private function getJavaScript($uniqeMapId) {
        return "
            <style type=\"text/css\">
                #" . $uniqeMapId . " {
                  width: 100%;
                  height: 480px;
                }
            </style>
            <script type=\"text/javascript\" src=\"https://maps.googleapis.com/maps/api/js?key=" . $this->mapsApiKey . "\"></script>
            <script type=\"text/javascript\">
                    //map.js

                    //Set up some of our variables.
                    var map; //Will contain map object.
                    var marker = false; ////Has the user plotted their location marker? 
                            
                    //Function called to initialize / create the map.
                    //This is called when the page has loaded.
                    function initMap() {
                        
                        var curLocation = $('input[name=\"data" . $this->data['elementBaseName'] . "\"]').first().val();
                        curLocation = curLocation.split(',');
                        var centerLat = curLocation[0] ? parseFloat(curLocation[0]) : 47.80453493591798;
                        var centerLng = curLocation[1] ? parseFloat(curLocation[1]) : 13.033771533827462;
                        
                        
                        //The center location of our map.
                        var centerOfMap = new google.maps.LatLng(centerLat, centerLng);
                    
                        //Map options.
                        var options = {
                          center: centerOfMap, //Set center.
                          zoom: 7 //The zoom value.
                        };
                    
                        //Create the map object.
                        map = new google.maps.Map(document.getElementById('" . $uniqeMapId . "'), options);
                        
                        marker = new google.maps.Marker({
                            position: centerOfMap,
                            map: map,
                            draggable: true //make it draggable
                        });
                        
                        
                        //Listen for drag events!
                        google.maps.event.addListener(marker, 'dragend', function(event){
                            markerLocation();
                        });
                        
                        
                        //Listen for any clicks on the map.
                        google.maps.event.addListener(map, 'click', function(event) {                
                            //Get the location that the user clicked.
                            var clickedLocation = event.latLng;
                            //If the marker hasn't been added.
                            if(marker === false){
                                //Create the marker.
                                marker = new google.maps.Marker({
                                    position: clickedLocation,
                                    map: map,
                                    draggable: true //make it draggable
                                });
                            } else{
                                //Marker has already been added, so just change its location.
                                marker.setPosition(clickedLocation);
                            }
                            //Get the marker's location.
                            markerLocation();
                        });
                    }
                            
                    //This function will get the marker's current location and then add the lat/long
                    //values to our textfields so that we can save the location.
                    function markerLocation(){
                        //Get location.
                        var currentLocation = marker.getPosition();
                        //Add lat and lng values to a field that we can save.
                        //document.getElementById('lat').value = currentLocation.lat(); //latitude
                        //document.getElementById('lng').value = currentLocation.lng(); //longitude
                        
                        $('input[name=\"data" . $this->data['elementBaseName'] . "\"]').first().val(currentLocation.lat() + ',' + currentLocation.lng());
                        $('input[data-formengine-input-name=\"data" . $this->data['elementBaseName'] . "\"]').first().val(currentLocation.lat() + ',' + currentLocation.lng());
                    }
                            
                            
                    //Load the map when the page has finished loading.
                    google.maps.event.addDomListener(window, 'load', initMap);
            </script>
            ";
    }
}