<?php
/**
 * Indicia, the OPAL Online Recording Toolkit.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see http://www.gnu.org/licenses/gpl.html.
 *
 * @package	Client
 * @subpackage PrebuiltForms
 * @author	Indicia Team
 * @license	http://www.gnu.org/licenses/gpl.html GPL 3.0
 * @link 	http://code.google.com/p/indicia/
 */

require_once('includes/map.php');
require_once('includes/language_utils.php');
require_once('includes/user.php');

/**
 * Prebuilt Indicia data entry form.
 * NB has Drupal specific code. Relies on presence of IForm loctools and IForm Proxy.
 *
 * @package	Client
 * @subpackage PrebuiltForms
 */

class iform_mnhnl_bird_transect_walks {

  /* TODO
   * Future Enhancements
   * 	General
   * 		Rename superuser to manager permission
   *      Separate the loading of the OCCList grid view from the population of the map.
   *      Change onShow for tabs to zoom into relevant area: eg location for survey, occlist extent for occlist
   * 	Survey List
   * 		Put in "loading" message functionality
   * 		Add filter by location
   * 	Location Allocation
   * 		Zoom map into location on request.
   *  Indicia Core
   *  	improve outputAttributes to handle restrict to survey correctly.
   *
   * The report paging will not be converted to use LIMIT & OFFSET because we want the full list returned so
   * we can display all the occurrences on the map.
   * When displaying transects, we should display children locations as well as parent.
   * 
   * Main Locations:
   * centroid is the grid square boundary surrounding the transect.
   * boundary is the actual transect walk.
   * Child Locations:
   * centroid is the buffer surrounding the transect.
   * boundary are the points defining the start and end of the walk.
   */
  /**
   * Get the list of parameters for this form.
   * @return array List of parameters that this form requires.
   */
  public static function get_parameters() {
  	// When deployed set map_height to 490
  	// map_width will be overridden to auto
  	// openlayers options needs to be filled in with projection {"projection":"900913"}
    return array_merge(
      iform_map_get_map_parameters(),      
      iform_user_get_user_parameters(), array(
      array(
        'name'=>'survey_id',
        'caption'=>'Survey ID',
        'description'=>'The Indicia ID of the survey that data will be posted into.',
        'type'=>'int'
      ),
      array(
        'name'=>'locationLayer',
        'caption'=>'Location Layer Definition',
        'description'=>'Comma separated list of option definitions for the location layer',
        'type'=>'string',
        'group'=>'Maps',
        'maxlength'=>200
      ),
      array(
	    'name'=>'map_projection',
	    'caption'=>'Map Projection (EPSG code)',
	    'description'=>'EPSG code to use for the map. If using 900913 then the preset layers such as Google maps will work, but for any other '.
	        'projection make sure that your base layers support it.',
	    'type'=>'string',
	    'default' => '900913',
	    'group'=>'Maps'
      ),
      array(
        'name'=>'sample_walk_direction_id',
        'caption'=>'Sample Walk Direction Custom Attribute ID',
        'description'=>'The Indicia ID for the Sample Custom Attribute for the Walk Direction.',
        'group'=>'Sample Attributes',
        'type'=>'int'
      ),
      array(
        'name'=>'sample_reliability_id',
        'caption'=>'Sample Data Reliability Custom Attribute ID',
        'description'=>'The Indicia ID for the Sample Custom Attribute for the Data Reliability.',
        'group'=>'Sample Attributes',
        'type'=>'int'
      ),
      array(
        'name'=>'sample_visit_number_id',
        'caption'=>'Sample Visit Number Custom Attribute ID',
        'description'=>'The Indicia ID for the Sample Custom Attribute for the Visit Number.',
        'group'=>'Sample Attributes',
        'type'=>'int'
      ),
      array(
        'name'=>'sample_wind_id',
        'caption'=>'Sample Wind Force Custom Attribute ID',
        'description'=>'The Indicia ID for the Sample Custom Attribute for the Wind Force.',
        'group'=>'Sample Attributes',
        'type'=>'int'
      ),
      array(
        'name'=>'sample_precipitation_id',
        'caption'=>'Sample Precipitation Custom Attribute ID',
        'description'=>'The Indicia ID for the Sample Custom Attribute for the Precipitation.',
        'group'=>'Sample Attributes',
        'type'=>'int'
      ),
      array(
        'name'=>'sample_temperature_id',
        'caption'=>'Sample Temperature Custom Attribute ID',
        'description'=>'The Indicia ID for the Sample Custom Attribute for the Temperature.',
        'group'=>'Sample Attributes',
        'type'=>'int'
      ),
      array(
        'name'=>'sample_cloud_id',
        'caption'=>'Sample Cloud Cover Custom Attribute ID',
        'description'=>'The Indicia ID for the Sample Custom Attribute for the Cloud Cover.',
        'group'=>'Sample Attributes',
        'type'=>'int'
      ),
      array(
        'name'=>'sample_start_time_id',
        'caption'=>'Sample Start Time Custom Attribute ID',
        'description'=>'The Indicia ID for the Sample Custom Attribute for the Start Time.',
        'group'=>'Sample Attributes',
        'type'=>'int'
      ),
      array(
        'name'=>'sample_end_time_id',
        'caption'=>'Sample End Time Custom Attribute ID',
        'description'=>'The Indicia ID for the Sample Custom Attribute for the End Time.',
        'group'=>'Sample Attributes',
        'type'=>'int'
      ),
      array(
        'name'=>'sample_closure_id',
        'caption'=>'Sample Closed Custom Attribute ID',
        'description'=>'The Indicia ID for the Sample Custom Attribute for Closure: this is used to determine whether the sample is editable.',
        'group'=>'Sample Attributes',
        'type'=>'int'
      ),
      array(
        'name'=>'list_id',
        'caption'=>'Species List ID',
        'description'=>'The Indicia ID for the species list that species can be selected from.',
        'type'=>'int'
      ),
      array(
        'name'=>'occurrence_confidence_id',
        'caption'=>'Occurrence Confidence Custom Attribute ID',
        'description'=>'The Indicia ID for the Occurrence Custom Attribute for the Data Confidence.',
        'group'=>'Occurrence Attributes',
        'type'=>'int'
      ),
      array(
        'name'=>'occurrence_count_id',
        'caption'=>'Occurrence Count Custom Attribute ID',
        'description'=>'The Indicia ID for the Occurrence Custom Attribute for the Count of the particular species.',
        'group'=>'Occurrence Attributes',
        'type'=>'int'
      ),
      array(
        'name'=>'occurrence_approximation_id',
        'caption'=>'Occurrence Approximation Custom Attribute ID',
        'description'=>'The Indicia ID for the Occurrence Custom Attribute for whether the count is approximate.',
        'group'=>'Occurrence Attributes',
        'type'=>'int'
      ),
      array(
        'name'=>'occurrence_territorial_id',
        'caption'=>'Occurrence Territorial Custom Attribute ID',
        'description'=>'The Indicia ID for the Occurrence Custom Attribute for whether the species is territorial.',
        'group'=>'Occurrence Attributes',
        'type'=>'int'
      ),
      array(
        'name'=>'occurrence_atlas_code_id',
        'caption'=>'Occurrence Atlas Code Custom Attribute ID',
        'description'=>'The Indicia ID for the Occurrence Custom Attribute for Altas Code.',
        'group'=>'Occurrence Attributes',
        'type'=>'int'
      ),
      array(
        'name'=>'occurrence_overflying_id',
        'caption'=>'Occurrence Overflying Custom Attribute ID',
        'description'=>'The Indicia ID for the Occurrence Custom Attribute for whether this sighting was flying overhead.',
        'group'=>'Occurrence Attributes',
        'type'=>'int'
      ),
      array(
        'name'=>'on_edit_survey_nav',
        'caption'=>'Navigation when choosing a survey to edit',
        'description'=>'Which Tab to display first when editing a survey (survey, occurrence, list)',
        'group'=>'Navigation',
        'type'=>'string',
	    'default' => 'survey',
      ),
      array(
        'name'=>'on_save_survey_nav',
        'caption'=>'Navigation when saving a survey',
        'description'=>'Which Tab to display after saving a survey (survey, occurrence, list)',
        'group'=>'Navigation',
        'type'=>'string',
	    'default' => 'occurrence',
      ),
      array(
        'name'=>'on_save_occurrence_nav',
        'caption'=>'Navigation when saving an occurrence',
        'description'=>'Which Tab to display after saving an occurrence (survey, occurrence, list)',
        'group'=>'Navigation',
        'type'=>'string',
	    'default' => 'occurrence',
      )
    ));
  }

  /**
   * Return the form title.
   * @return string The title of the form.
   */
  public static function get_title() {
    return 'MNHNL Bird Transect Walks';
  }

  public static function get_perms($nid) {
    return array('IForm node '.$nid.' admin');
  }

  /**
   * Return the generated form output.
   * @return Form HTML.
   */
  public static function get_form($args, $node, $response=null) {
    global $user;
    global $custom_terms;
    $logged_in = $user->uid>0;
    $r = '';

    // Get authorisation tokens to update and read from the Warehouse.
    $auth = data_entry_helper::get_read_write_auth($args['website_id'], $args['password']);
    $readAuth = $auth['read'];
    $svcUrl = data_entry_helper::$base_url.'/index.php/services';

    drupal_add_js(drupal_get_path('module', 'iform') .'/media/js/jquery.form.js', 'module');
    data_entry_helper::link_default_stylesheet();
    data_entry_helper::add_resource('jquery_ui');
    $language = iform_lang_iso_639_2($args['language']);
    if($args['language'] != 'en')
        data_entry_helper::add_resource('jquery_ui_'.$args['language']);
    
    // If not logged in: Display an information message.
    // This form should only be called in POST mode when setting the location allocation.
    //  All other posting is now done via AJAX.
    // When invoked by GET there are the following modes:
    // No additional arguments: mode 0.
    // Additional argument - newSample: mode 1.
    // Additional argument - sample_id=<id>: mode 2.
    // Additional argument - occurrence_id=<id>: mode 3.
    // Additional arguments - merge_sample_id1=<id>&merge_sample_id2=<id> : mode 2.1
    $mode = 0; // default mode : output survey selector
          // mode 1: output the main Data Entry page: occurrence list or add/edit occurrence tabs hidden. "Survey" tab active
          // mode 2: output the main Data Entry page, display existing sample. Active tab determined by iform params. No occurence details filled in.
          // mode 2.1: sample 2 has all its occurrences merged into sample 1. sample 2 is then flagged as deleted. sample 1 is then viewed as in normal mode 2.
          // mode 3: output the main Data Entry page, display existing occurrence. "Edit Occurrence" tab active. Occurence details filled in.

    $surveyReadOnly = false; // On top of this, things can be flagged as readonly. RO mode 2+4 means no Add Occurrence tab.
    if (!$logged_in){
      return lang::get('LANG_not_logged_in');
    }
    $parentSample = array();
    $parentLoadID = null;
    $childSample = array();
    $childLoadID = null;
    $thisOccID=-1; // IDs have to be >0, so this is outside the valid range
    $adminPerm = 'IForm node '.$node->nid.' admin';
    
    if ($_POST) {
      if(!array_key_exists('website_id', $_POST)) { // non Indicia POST, in this case must be the location allocations. add check to ensure we don't corrept the data by accident
        if(iform_loctools_checkaccess($node,'admin') && array_key_exists('mnhnlbtw', $_POST)){
          iform_loctools_deletelocations($node);
          foreach($_POST as $key => $value){
            $parts = explode(':', $key);
            if($parts[0] == 'location' && $value){
              iform_loctools_insertlocation($node, $value, $parts[1]);
            }
          }
        }
      }
    } else {
      if (array_key_exists('merge_sample_id1', $_GET) && array_key_exists('merge_sample_id2', $_GET) && user_access($adminPerm)){
        $mode = 2;
        // first check can access the 2 samples given
        $parentLoadID = $_GET['merge_sample_id1'];
        $url = $svcUrl.'/data/sample/'.$parentLoadID."?mode=json&view=detail&auth_token=".$readAuth['auth_token']."&nonce=".$readAuth["nonce"];
        $session = curl_init($url);
        curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
        $entity = json_decode(curl_exec($session), true);
        if(count($entity)==0 || $entity[0]["parent_id"])
          return '<p>'.lang::get('LANG_No_Access_To_Sample').' '.$parentLoadID.'</p>';
        // The check for id2 is slightly different: there is the possiblity that someone will F5/refresh their browser, after the transfer and delete have taken place.
        // In this case we carry on, but do not do the transfer and delete.
        $url = $svcUrl.'/data/sample/'.$_GET['merge_sample_id2']."?mode=json&view=detail&auth_token=".$readAuth['auth_token']."&nonce=".$readAuth["nonce"];
        $session = curl_init($url);
        curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
        $entity = json_decode(curl_exec($session), true);
        if(count($entity)>0 && !$entity[0]["parent_id"]) {
          // now get child samples and point to new parent.
          $url = $svcUrl.'/data/sample?mode=json&view=detail&auth_token='.$readAuth['auth_token']."&nonce=".$readAuth["nonce"].'&parent_id='.$_GET['merge_sample_id2'];
          $session = curl_init($url);
          curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
          $entities = json_decode(curl_exec($session), true);
          if(count($entities)>0){
            foreach($entities as $entity){
              $Model = data_entry_helper::wrap(array('id'=>$entity['id'], 'parent_id' => $_GET['merge_sample_id1']), 'sample');
              $request = data_entry_helper::$base_url."/index.php/services/data/save";
              $postargs = 'submission='.json_encode($Model).'&auth_token='.$auth['write_tokens']['auth_token'].'&nonce='.$auth['write_tokens']['nonce'].'&persist_auth=true';
              $postresponse = data_entry_helper::http_post($request, $postargs, false);
              // the response array will always feature an output, which is the actual response or error message. if it is not json format, assume error text, and json encode that.
              $response = $postresponse['output'];
              if (!json_decode($response, true))
                return "<p>".lang::get('LANG_Error_When_Moving_Sample').": id ".$entity['id']." : ".$response;
            }
          }
          // finally delete the no longer used sample
          $Model = data_entry_helper::wrap(array('id'=>$_GET['merge_sample_id2'], 'deleted' => 'true'), 'sample');
          $request = data_entry_helper::$base_url."/index.php/services/data/save";
          $postargs = 'submission='.json_encode($Model).'&auth_token='.$auth['write_tokens']['auth_token'].'&nonce='.$auth['write_tokens']['nonce'].'&persist_auth=true';
          $postresponse = data_entry_helper::http_post($request, $postargs, false);
          // the response array will always feature an output, which is the actual response or error message. if it is not json format, assume error text, and json encode that.
          $response = $postresponse['output'];
          if (!json_decode($response, true))
            return "<p>".lang::get('LANG_Error_When_Deleting_Sample').": id ".$entity['id']." : ".$response;
        }
      } else if (array_key_exists('sample_id', $_GET)){
        $mode = 2;
        $parentLoadID = $_GET['sample_id'];
      } else if (array_key_exists('occurrence_id', $_GET)){
        $mode = 3;
        $childLoadID = $_GET['occurrence_id'];
        $thisOccID = $childLoadID;
      } else if (array_key_exists('newSample', $_GET)){
        $mode = 1;
      } // else default to mode 0
    }

    // define language strings so they can be used for validation translation.
    data_entry_helper::$javascript .= "var translations = [\n";
    foreach($custom_terms as $key => $value){
      if(substr($key, 0, 4) != "LANG") data_entry_helper::$javascript .= "  {key: \"".$key."\", translated: \"".$value."\"},\n";
    }
    data_entry_helper::$javascript .= "];\n";
    // define layers for all maps.
    // each argument is a comma separated list eg:
    // "Name:Lux Outline,URL:http://localhost/geoserver/wms,LAYERS:indicia:nation2,SRS:EPSG:2169,FORMAT:image/png,minScale:0,maxScale:1000000,units:m";
    $optionsArray_Location = array();
    $options = explode(',', $args['locationLayer']);
    foreach($options as $option){
      $parts = explode(':', $option);
      $optionName = $parts[0];
      unset($parts[0]);
      $optionsArray_Location[$optionName] = implode(':', $parts);
    }
    // Work out list of locations this user can see.
    $locations = iform_loctools_listlocations($node);
    if($locations != 'all'){
        data_entry_helper::$javascript .= "locationList = [".implode(',', $locations)."];\n";
    }
    data_entry_helper::$javascript .= "
// Create Layers.
// Base Layers first.
WMSoptions = {
          SERVICE: 'WMS',
          VERSION: '1.1.0',
          STYLES: '',
          SRS: '".$optionsArray_Location['SRS']."',
          FORMAT: '".$optionsArray_Location['FORMAT']."',
          TRANSPARENT: 'true', ";
    if($locations != 'all'){
      // when given a restricted feature list we have to use the feature id to filter in order to not go over 2000 char limit on the URL
      // Can only generate the feature id if we access a table directly, not through a view. Go direct to the locations table.
      // don't need to worry about parent_id in this case as we know exactly which features we want.
      // need to use btw_transects view for unrestricted so we can filter by parent_id=NULL.
      $locFeatures = array();
      foreach($locations as $location)
        $locFeatures[] = "locations.".$location;
      data_entry_helper::$javascript .= "
        LAYERS: 'indicia:locations',
        FEATUREID: '".implode(',', $locFeatures)."'";
    } else {
      data_entry_helper::$javascript .= "
        LAYERS: '".$optionsArray_Location['LAYERS']."'";
      // TBD add filter  for website_id.
    }
    data_entry_helper::$javascript .= "
    };
locationListLayer = new OpenLayers.Layer.WMS('".$optionsArray_Location['Name']."',
        '".iform_proxy_url($optionsArray_Location['URL'])."',
        WMSoptions, {
             minScale: ".$optionsArray_Location['minScale'].",
            maxScale: ".$optionsArray_Location['maxScale'].",
            units: '".$optionsArray_Location['units']."',
            isBaseLayer: false,
            singleTile: true
        });
// Create vector layers: one to display the location onto, and another for the occurrence list
// the default edit layer is used for the occurrences themselves
locStyleMap = new OpenLayers.StyleMap({
                \"default\": new OpenLayers.Style({
                    fillColor: \"Green\",
                    strokeColor: \"Black\",
                    fillOpacity: 0.2,
                    strokeWidth: 1
                  })
  });
locationLayer = new OpenLayers.Layer.Vector(\"".lang::get("LANG_Location_Layer")."\",
                                    {styleMap: locStyleMap});
occStyleMap = new OpenLayers.StyleMap({
                \"default\": new OpenLayers.Style({
                    pointRadius: 3,
                    fillColor: \"Red\",
                    fillOpacity: 0.3,
                    strokeColor: \"Red\",
                    strokeWidth: 1
          }) });
occListLayer = new OpenLayers.Layer.Vector(\"".lang::get("LANG_Occurrence_List_Layer")."\",
                                    {styleMap: occStyleMap});
";
    drupal_add_js(drupal_get_path('module', 'iform') .'/media/js/hasharray.js', 'module');
    drupal_add_js(drupal_get_path('module', 'iform') .'/media/js/jquery.datagrid.js', 'module');

    // Work out list of locations this user can see.
    $locations = iform_loctools_listlocations($node);
    ///////////////////////////////////////////////////////////////////
    // default mode 0 : display a page with tabs for survey selector,
    // locations allocator and reports (last two require permissions)
    ///////////////////////////////////////////////////////////////////
    if($mode == 0){

      // If the user has permissions, add tabs so can choose to see
      // locations allocator
      $tabs = array('#surveyList'=>lang::get('LANG_Surveys'));
      if(iform_loctools_checkaccess($node,'admin')){
        $tabs['#setLocations'] = lang::get('LANG_Allocate_Locations');
      }
      if(iform_loctools_checkaccess($node,'superuser')){
        $tabs['#downloads'] = lang::get('LANG_Download');
      }
      if(count($tabs) > 1){
        $r .= "<div id=\"controls\">".(data_entry_helper::enable_tabs(array('divId'=>'controls','active'=>'#surveyList')))."<div id=\"temp\"></div>";
        $r .= data_entry_helper::tab_header(array('tabs'=>$tabs));
      }

      if($locations == 'all'){
        $useloclist = 'NO';
        $loclist = '-1';
      } else {
        // an empty list will cause an sql error, lids must be > 0, so push a -1 to prevent the error.
        if(empty($locations)) $locations[] = -1;
        $useloclist = 'YES';
        $loclist = implode(',', $locations);
      }

    // Create the Survey list datagrid for this user.
      drupal_add_js("jQuery(document).ready(function(){
  $('div#smp_grid').indiciaDataGrid('rpt:mnhnl_btw_list_samples', {
    indiciaSvc: '".$svcUrl."',
    dataColumns: ['location_name', 'date', 'num_visit', 'num_occurrences', 'num_taxa'],
    reportColumnTitles: {location_name : '".lang::get('LANG_Transect')."', date : '".lang::get('LANG_Date')."', num_visit : '".lang::get('LANG_Visit_No')."', num_occurrences : '".lang::get('LANG_Num_Occurrences')."', num_taxa : '".lang::get('LANG_Num_Species')."'},
    actionColumns: {".lang::get('LANG_Show')." : \"".url('node/'.($node->nid), array('query' => 'sample_id=�id�'))."\"},
    auth : { nonce : '".$readAuth['nonce']."', auth_token : '".$readAuth['auth_token']."'},
    parameters : { survey_id : '".$args['survey_id']."', visit_attr_id : '".$args['sample_visit_number_id']."', closed_attr_id : '".$args['sample_closure_id']."', use_location_list : '".$useloclist."', locations : '".$loclist."'},
    itemsPerPage : 12,
    condCss : {field : 'closed', value : '0', css: 'mnhnl-btw-highlight'},
    cssOdd : ''
  });
});
      ", 'inline');
      $r .= '
  <div id="surveyList" class="mnhnl-btw-datapanel"><div id="smp_grid"></div>
    <form><input type="button" value="'.lang::get('LANG_Add_Survey').'" onclick="window.location.href=\''.url('node/'.($node->nid), array('query' => 'newSample')).'\'"></form></div>';
      // Add the locations allocator if user has admin rights.
      if(iform_loctools_checkaccess($node,'admin')){
        $r .= '
  <div id="setLocations" class="mnhnl-btw-datapanel">
    <form method="post">
      <input type="hidden" id="mnhnlbtw" name="mnhnlbtw" value="mnhnlbtw" />';
        $url = $svcUrl.'/data/location?mode=json&view=detail&auth_token='.$readAuth['auth_token']."&nonce=".$readAuth["nonce"]."&parent_id=NULL&orderby=name";
        $session = curl_init($url);
        curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
        $entities = json_decode(curl_exec($session), true);
        $userlist = iform_loctools_listusers($node);
        if(!empty($entities)){
          foreach($entities as $entity){
            if(!$entity["parent_id"]){ // only assign parent locations.
              $r .= "\n<label for=\"location:".$entity["id"]."\">".$entity["name"].":</label><select id=\"location:".$entity["id"]."\" name=\"location:".$entity["id"]."\"><option value=\"\" >&lt;".lang::get('LANG_Not_Allocated')."&gt;</option>";
              $defaultuserid = iform_loctools_getuser($node, $entity["id"]);
              foreach($userlist as $uid => $a_user){
                $r .= "<option value=\"".$uid."\" ".($uid == $defaultuserid ? 'selected="selected" ' : '').">".$a_user->name."</option>";
              }
              $r .= "</select>";
            }
          }
        }
        $r .= "
      <input type=\"submit\" class=\"ui-state-default ui-corner-all\" value=\"".lang::get('LANG_Save_Location_Allocations')."\" />
    </form>
  </div>";
      }
      // Add the downloader if user has manager (superuser) rights.
      if(iform_loctools_checkaccess($node,'superuser')){
        $r .= '
  <div id="downloads" class="mnhnl-btw-datapanel">
    <form method="post" action="'.data_entry_helper::$base_url.'/index.php/services/report/requestReport?report=mnhnl_btw_transect_direction_report.xml&reportSource=local&auth_token='.$readAuth['auth_token'].'&nonce='.$readAuth['nonce'].'&mode=csv">
      <p>'.lang::get('LANG_Direction_Report').'</p>
      <input type="hidden" id="params" name="params" value=\'{"survey_id":'.$args['survey_id'].', "direction_attr_id":'.$args['sample_walk_direction_id'].', "closed_attr_id":'.$args['sample_closure_id'].'}\' />
      <input type="submit" class="ui-state-default ui-corner-all" value="'.lang::get('LANG_Direction_Report_Button').'">
    </form>
    <form method="post" action="'.data_entry_helper::$base_url.'/index.php/services/report/requestReport?report=mnhnl_btw_download_report.xml&reportSource=local&auth_token='.$readAuth['auth_token'].'&nonce='.$readAuth['nonce'].'&mode=csv">
      <p>'.lang::get('LANG_Initial_Download').'</p>
      <input type="hidden" id="params" name="params" value=\'{"survey_id":'.$args['survey_id'].', "closed_attr_id":'.$args['sample_closure_id'].', "download": "INITIAL"}\' />
      <input type="submit" class=\"ui-state-default ui-corner-all" value="'.lang::get('LANG_Initial_Download_Button').'">
    </form>
    <form method="post" action="'.data_entry_helper::$base_url.'/index.php/services/report/requestReport?report=mnhnl_btw_download_report.xml&reportSource=local&auth_token='.$readAuth['auth_token'].'&nonce='.$readAuth['nonce'].'&mode=csv">
      <p>'.lang::get('LANG_Confirm_Download').'</p>
      <input type="hidden" id="params" name="params" value=\'{"survey_id":'.$args['survey_id'].', "closed_attr_id":'.$args['sample_closure_id'].', "download": "CONFIRM"}\' />
      <input type="submit" class="ui-state-default ui-corner-all" value="'.lang::get('LANG_Confirm_Download_Button').'">
    </form>
    <form method="post" action="'.data_entry_helper::$base_url.'/index.php/services/report/requestReport?report=mnhnl_btw_download_report.xml&reportSource=local&auth_token='.$readAuth['auth_token'].'&nonce='.$readAuth['nonce'].'&mode=csv">
      <p>'.lang::get('LANG_Final_Download').'</p>
      <input type="hidden" id="params" name="params" value=\'{"survey_id":'.$args['survey_id'].', "closed_attr_id":'.$args['sample_closure_id'].', "download": "FINAL"}\' />
      <input type="submit" class="ui-state-default ui-corner-all" value="'.lang::get('LANG_Final_Download_Button').'">
    </form>
  </div>';
      }
      // Create Map
      $options = iform_map_get_map_options($args, $readAuth);
      $olOptions = iform_map_get_ol_options($args);
      $options['layers'] = array('locationListLayer');
      $options['searchLayer'] = 'false';
      $options['editLayer'] = 'false';
      $options['initialFeatureWkt'] = null;
      $options['proxy'] = '';
      $options['scroll_wheel_zoom'] = false;
      $options['width'] = 'auto'; // TBD remove from arglist
      $r .= "<div class=\"mnhnl-btw-mappanel\">\n".(data_entry_helper::map_panel($options, $olOptions))."</div>\n";

      data_entry_helper::$javascript .= "
$('#controls').bind('tabsshow', function(event, ui) {
  var y = $('.mnhnl-btw-datapanel:visible').outerHeight(true) + $('.mnhnl-btw-datapanel:visible').position().top;
  if(y < $('.mnhnl-btw-mappanel').outerHeight(true)+ $('.mnhnl-btw-mappanel').position().top){
    y = $('.mnhnl-btw-mappanel').outerHeight(true)+ $('.mnhnl-btw-mappanel').position().top;
  }
  $('#controls').height(y - $('#controls').position().top);
});
";
      if(count($tabs)>1){ // close tabs div if present
        $r .= "</div>";
      }
      return $r;
    }
    ///////////////////////////////////////////////////////////////////
    // At this point there are 3 modes:
    // Adding a new survey
    // editing/showing an existing survey
    // editing/showing an existing occurrence
    // First load the occurrence (and its position sample) if provided
    // Then load the parent sample if provided, or derived from occurrence.
    // $occReadOnly is set if the occurrence has been downloaded. Not even an admin user can modify it in this case.
    $occReadOnly = false;
    $childSample = array();
    if($childLoadID){ // load the occurrence and its associated sample (which holds the position)
      $url = $svcUrl.'/data/occurrence/'.$childLoadID;
      $url .= "?mode=json&view=detail&auth_token=".$readAuth['auth_token']."&nonce=".$readAuth["nonce"];
      $session = curl_init($url);
      curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
      $entity = json_decode(curl_exec($session), true);
      if(count($entity)==0){
        return '<p>'.lang::get('LANG_No_Access_To_Occurrence').'</p>';
      }
      foreach($entity[0] as $key => $value){
        $childSample['occurrence:'.$key] = $value;
      }
      if($entity[0]['downloaded_flag'] == 'F') { // Final download complete, now readonly
        $occReadOnly = true;
      }
      $url = $svcUrl.'/data/sample/'.$childSample['occurrence:sample_id'];
      $url .= "?mode=json&view=detail&auth_token=".$readAuth['auth_token']."&nonce=".$readAuth["nonce"];
      $session = curl_init($url);
      curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
      $entity = json_decode(curl_exec($session), true);
      if(count($entity)==0){
        return '<p>'.lang::get('LANG_No_Access_To_Occurrence').'</p>';
      }
      foreach($entity[0] as $key => $value){
        $childSample['sample:'.$key] = $value;
      }
      $childSample['sample:geom'] = ''; // value received from db is not WKT, which is assumed by all the code.
      $childSample['taxon']=$childSample['occurrence:taxon'];
      $parentLoadID=$childSample['sample:parent_id'];
    }
    $parentSample = array();
    if($parentLoadID){ // load the container master sample
      $url = $svcUrl.'/data/sample/'.$parentLoadID;
      $url .= "?mode=json&view=detail&auth_token=".$readAuth['auth_token']."&nonce=".$readAuth["nonce"];
      $session = curl_init($url);
      curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
      $entity = json_decode(curl_exec($session), true);
      if(count($entity)==0){
        return '<p>'.lang::get('LANG_No_Access_To_Sample').'</p>';
      }
      foreach($entity[0] as $key => $value){
        $parentSample['sample:'.$key] = $value;
      }
      if(is_array($locations) && !in_array($entity[0]["location_id"], $locations)){
        return '<p>'.lang::get('LANG_No_Access_To_Location').'</p>';
      }
      if($entity[0]["parent_id"]){
        return '<p>'.lang::get('LANG_No_Access_To_Sample').'</p>';
      }
      $parentSample['sample:date'] = $parentSample['sample:date_start']; // bit of a bodge
      $childSample['sample:date'] = $parentSample['sample:date']; // enforce a match between child and parent sample dates
      // default values for attributes from DB are picked up automatically.
    }

    data_entry_helper::$entity_to_load=$parentSample;
    $attributes = data_entry_helper::getAttributes(array(
      'id' => data_entry_helper::$entity_to_load['sample:id']
       ,'valuetable'=>'sample_attribute_value'
       ,'attrtable'=>'sample_attribute'
       ,'key'=>'sample_id'
       ,'fieldprefix'=>'smpAttr'
       ,'extraParams'=>$readAuth
    ));
    $closedFieldName = $attributes[$args['sample_closure_id']]['fieldname'];
    $closedFieldValue = data_entry_helper::check_default_value($closedFieldName, array_key_exists('default', $attributes[$args['sample_closure_id']]) ? $attributes[$args['sample_closure_id']]['default'] : '0'); // default is not closed
    if($closedFieldValue == '1' && !user_access($adminPerm)){
      // sample has been closed, no admin perms. Everything now set to read only.
      $surveyReadOnly = true;
      $disabledText = "disabled=\"disabled\"";
      $defAttrOptions = array('extraParams'=>$readAuth,
                  'disabled'=>$disabledText);
    } else {
      // sample editable. Admin users can modify closed samples.
      $disabledText="";
      $defAttrOptions = array('extraParams'=>$readAuth);
    }

    // with the AJAX code, we deal with the validation semi manually: Form name is meant be invalid as we only want code included.
    data_entry_helper::enable_validation('DummyForm');
    $r .= "<div id=\"controls\">\n";
    $activeTab = 'survey'; // mode 1 = new Sample, display sample. 
    if($mode == 2){ // have specified a sample ID
      if($args["on_edit_survey_nav"] == "survey")
        $activeTab = 'survey';
      else if($surveyReadOnly || $args["on_edit_survey_nav"] == "list")
        $activeTab = 'occurrenceList';
      else $activeTab = 'occurrence';
      if($surveyReadOnly)
        data_entry_helper::$javascript .= "jQuery('#occ-form').hide();";
    } else if($mode == 3) // have specified an occurrence ID
      $activeTab = 'occurrence';

    // Set Up form tabs.
    $r .= data_entry_helper::enable_tabs(array(
        'divId'=>'controls',
      'active'=>$activeTab
    ));
    $r .= "<div id=\"temp\"></div>";
    $r .= data_entry_helper::tab_header(array('tabs'=>array(
        '#survey'=>lang::get('LANG_Survey')
        ,'#occurrence'=>lang::get(($surveyReadOnly || $occReadOnly) ? 'LANG_Show_Occurrence' : (isset($childSample['sample:id']) ?  'LANG_Edit_Occurrence' : 'LANG_Add_Occurrence'))
        ,'#occurrenceList'=>lang::get('LANG_Occurrence_List')
        )));

    // Set up main Survey Form.
    $r .= "<div id=\"survey\" class=\"mnhnl-btw-datapanel\">
  <p id=\"read-only-survey\"><strong>".lang::get('LANG_Read_Only_Survey')."</strong></p>";
    if(user_access($adminPerm) && array_key_exists('sample:id', data_entry_helper::$entity_to_load)) {
    	// check for other surveys of same date/transect: only if admin user.
        $url = $svcUrl.'/data/sample?mode=json&view=detail&auth_token='.$readAuth['auth_token']."&nonce=".$readAuth["nonce"]."&date_start=".$parentSample['sample:date_start']."&location_id=".$parentSample['sample:location_id'];
        $session = curl_init($url);
        curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
        $entity = json_decode(curl_exec($session), true);
        if(count($entity)>1){ // ignore ourselves!
        	$r .= "<div id=\"mergeSurveys\"><p><strong>".lang::get('LANG_Found_Mergable_Surveys')."</strong></p>";
        	foreach($entity as $survey){
        		if($survey['id'] != $parentSample['sample:id']){
                  $r .= "<form action=\"".url('node/'.($node->nid), array())."\" method=\"get\"><input type=\"submit\" value=\"".lang::get('LANG_Merge_With_ID')." ".$survey['id']."\"><input type=\"hidden\" name=\"merge_sample_id1\" value=\"".$parentSample['sample:id']."\" /><input type=\"hidden\" name=\"merge_sample_id2\" value=\"".$survey['id']."\" /></form>";
        		}
        	}
        	$r .= "</div>";
        }    	
    }
    $r .= "<form id=\"SurveyForm\" action=\"".iform_ajaxproxy_url($node, 'sample')."\" method=\"post\">
    <input type=\"hidden\" id=\"website_id\" name=\"website_id\" value=\"".$args['website_id']."\" />
    <input type=\"hidden\" id=\"sample:survey_id\" name=\"sample:survey_id\" value=\"".$args['survey_id']."\" />";
    if(array_key_exists('sample:id', data_entry_helper::$entity_to_load)){
      $r .= "<input type=\"hidden\" id=\"sample:id\" name=\"sample:id\" value=\"".data_entry_helper::$entity_to_load['sample:id']."\" />\n";
    } else {
      $r .= "<input type=\"hidden\" id=\"sample:id\" name=\"sample:id\" value=\"\" disabled=\"disabled\" />\n";
    }

    $fieldName = $attributes[$args['uid_attr_id']]['fieldname'];
    $fieldValue = data_entry_helper::check_default_value($fieldName, $user->uid);
    $r .= "<input type=\"hidden\" name=\"".$fieldName."\" value=\"".$fieldValue."\" />\n";
    $fieldName = $attributes[$args['email_attr_id']]['fieldname'];
    $fieldValue = data_entry_helper::check_default_value($fieldName, $user->mail);
    $r .= "<input type=\"hidden\" name=\"".$fieldName."\" value=\"".$fieldValue."\" />\n";
    $fieldName = $attributes[$args['username_attr_id']]['fieldname'];
    $fieldValue = data_entry_helper::check_default_value($fieldName, $user->name);
    $r .= "<input type=\"hidden\" name=\"".$fieldName."\" value=\"".$fieldValue."\" />\n";
      
    $defAttrOptions['validation'] = array('required');
    $defAttrOptions['suffixTemplate']='requiredsuffix';
    if($locations == 'all'){
      $locOptions = array_merge(array('label'=>lang::get('LANG_Transect')), $defAttrOptions);
      $locOptions['extraParams'] = array_merge(array('parent_id'=>'NULL', 'view'=>'detail', 'orderby'=>'name'), $locOptions['extraParams']);
      $r .= data_entry_helper::location_select($locOptions);
    } else {
      // can't use location select due to location filtering.
      $r .= "<label for=\"imp-location\">".lang::get('LANG_Transect').":</label>\n<select id=\"imp-location\" name=\"sample:location_id\" ".$disabled_text." class=\" \"  >";
      $url = $svcUrl.'/data/location?mode=json&view=detail&parent_id=NULL&orderby=name&auth_token='.$readAuth['auth_token'].'&nonce='.$readAuth["nonce"];
      $session = curl_init($url);
      curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
      $entities = json_decode(curl_exec($session), true);
      if(!empty($entities)){
        foreach($entities as $entity){
          if(in_array($entity["id"], $locations)) {
            if($entity["id"] == data_entry_helper::$entity_to_load['sample:location_id']) {
              $selected = 'selected="selected"';
            } else {
              $selected = '';
            }
            $r .= "<option value=\"".$entity["id"]."\" ".$selected.">".$entity["name"]."</option>";
          }
        }
      }
      $r .= "</select><span class=\"deh-required\">*</span><br />";
    }
	$languageFilteredAttrOptions = $defAttrOptions + array('language' => iform_lang_iso_639_2($args['language']));
    $r .= data_entry_helper::outputAttribute($attributes[$args['sample_walk_direction_id']], $languageFilteredAttrOptions).
          data_entry_helper::outputAttribute($attributes[$args['sample_reliability_id']], $languageFilteredAttrOptions).
          data_entry_helper::outputAttribute($attributes[$args['sample_visit_number_id']], array_merge($languageFilteredAttrOptions, array('default'=>1, 'noBlankText'=>true)));
    if($surveyReadOnly) {
      $r .= data_entry_helper::text_input(array_merge($defAttrOptions, array('label' => lang::get('LANG_Date'), 'fieldname' => 'sample:date', 'disabled'=>$disabledText )));
    } else {
      $r .= data_entry_helper::date_picker(array('label' => lang::get('LANG_Date'), 'fieldname' => 'sample:date', 'class' => 'vague-date-picker', 'suffixTemplate'=>'requiredsuffix'));
    }
    $r .= data_entry_helper::outputAttribute($attributes[$args['sample_wind_id']], $languageFilteredAttrOptions).
          data_entry_helper::outputAttribute($attributes[$args['sample_precipitation_id']], $languageFilteredAttrOptions).
          data_entry_helper::outputAttribute($attributes[$args['sample_temperature_id']], array_merge($defAttrOptions, array('suffixTemplate'=>'nosuffix')))." degC<span class=\"deh-required\">*</span><br />".
          data_entry_helper::outputAttribute($attributes[$args['sample_cloud_id']], $defAttrOptions).
          data_entry_helper::outputAttribute($attributes[$args['sample_start_time_id']], array_merge($defAttrOptions, array('suffixTemplate'=>'nosuffix')))." hh:mm<span class=\"deh-required\">*</span><br />".
          data_entry_helper::outputAttribute($attributes[$args['sample_end_time_id']], array_merge($defAttrOptions, array('suffixTemplate'=>'nosuffix')))." hh:mm<span class=\"deh-required\">*</span><br />";
    unset($defAttrOptions['suffixTemplate']);
    unset($defAttrOptions['validation']);
    if(user_access($adminPerm)) { //  users with admin permissions can override the closing of the
      // sample by unchecking the checkbox.
      // Because this is attached to the sample, we have to include the sample required fields in the
      // the post. This means they can't be disabled, so we enable all fields in this case.
      // Normal users can only set this to closed, and they do this using a button/hidden field.
      $r .= data_entry_helper::outputAttribute($attributes[$args['sample_closure_id']], $defAttrOptions);
      // In addition admin users can delete a survey/sample.
      $r .= data_entry_helper::checkbox(array('label'=>lang::get('Deleted'), 'fieldname'=>'sample:deleted', 'id'=>'main-sample-deleted'));
    } else {
      // hidden closed
      $r .= "<input type=\"hidden\" id=\"main-sample-closed\" name=\"".$closedFieldName."\" value=\"".$closedFieldValue."\" />\n";
    }
	data_entry_helper::$javascript .= "
$.validator.messages.required = \"".lang::get('validation_required')."\";
$.validator.defaults.onsubmit = false; // override default - so that we handle all submission validation.
";

    
    if(!$surveyReadOnly){
      // NB that we don't even include the buttons when readonly.
      data_entry_helper::$javascript .= "
jQuery('#read-only-survey').hide();
jQuery('#ro-sur-occ-warn').hide();
";
      $r .= "<input type=button id=\"close1\" class=\"ui-state-default ui-corner-all \" value=\"".lang::get('LANG_Save_Survey_Details')."\";
        onclick=\"var result = $('#SurveyForm input').valid();
          var result2 = $('#SurveyForm select').valid();
          if (!result || !result2) {
              return;
            }
            jQuery('#close1').addClass('loading-button');
            jQuery('#SurveyForm').submit();\">\n";
      if(!user_access($adminPerm)) {
      	if($mode == 1) data_entry_helper::$javascript .= "jQuery('#close2').hide();\n";
       $r .= "<input type=button id=\"close2\" class=\"ui-state-default ui-corner-all \" value=\"".lang::get('LANG_Save_Survey_And_Close')."\"
        onclick=\"if(confirm('".lang::get('LANG_Close_Survey_Confirm')."')){
          var result = $('#SurveyForm input').valid();
          var result2 = $('#SurveyForm select').valid();
          if (!result || !result2) {
              return;
            }
            jQuery('#main-sample-closed').val('1');
            jQuery('#close2').addClass('loading-button');
            jQuery('#SurveyForm').submit();
          };\">\n";
      }
    }
    $r .= "</form></div>\n";
    data_entry_helper::$javascript .= "
alertIndiciaError = function(data){
	var errorString = \"".lang::get('LANG_Indicia_Warehouse_Error')."\";
	if(data.error){	errorString = errorString + ' : ' + data.error;	}
	if(data.errors){
		for (var i in data.errors){
			errorString = errorString + ' : ' + data.errors[i];
		}				
	}
	alert(errorString);
	// the most likely cause is authentication failure - eg the read authentication has timed out.
	// prevent further use of the form:
	$('.loading-panel').remove();
	$('.loading-hide').removeClass('loading-hide');
};
errorPos = null;
clearErrors = function(formSel) {
	jQuery(formSel).find('.inline-error').remove();
	errorPos = null;
};
myScrollTo = function(selector){
	jQuery(selector).filter(':visible').each(function(){
		if(errorPos == null || jQuery(this).offset().top < errorPos){
			errorPos = jQuery(this).offset().top;
			window.scroll(0,errorPos);
		}
	});
};
myScrollToError = function(){
	jQuery('.inline-error,.error').filter(':visible').prev().each(function(){
		if(errorPos == null || jQuery(this).offset().top < errorPos){
			errorPos = jQuery(this).offset().top;
			window.scroll(0,errorPos);
		}
	});
};
jQuery('#SurveyForm').ajaxForm({ 
	async: false,
	dataType:  'json', 
    beforeSubmit:   function(data, obj, options){
    	var valid = true;
    	clearErrors('form#SurveyForm');
    	if (!jQuery('form#SurveyForm > input').valid()) {
			myScrollToError();
  			jQuery('.loading-button').removeClass('loading-button');
			return false;
  		};
  		SurveyFormRetVal = true;
  		if(jQuery('#main-sample-deleted:checked').length == 0){ // only do check if not deleting
          jQuery.ajax({ // now check if there are any other samples with this combination of date and location
            type: 'GET', 
            url: \"".$svcUrl."/data/sample?mode=json&view=detail\" +
                \"&nonce=".$readAuth['nonce']."&auth_token=".$readAuth['auth_token']."\" +
                \"&orderby=id&callback=?&location_id=\"+jQuery('#imp-location').val()+\"&date_start=\"+jQuery('#SurveyForm [name=sample\\:date]').val(), 
            data: {}, 
            success: function(detData) {
              for(i=0, j=0; i< detData.length; i++){
                if(detData[i].id != jQuery('#SurveyForm [name=sample\\:id]').val()) j++;
              }
              if(j) {
              	SurveyFormRetVal = confirm(\"".lang::get('LANG_Survey_Already_Exists')."\");
              }
            },
            dataType: 'json', 
            async: false 
          });
        } 
		return SurveyFormRetVal;
	},
    success:   function(data){
       // this will leave all the fields populated.
       	if(data.success == 'multiple records' && data.outer_table == 'sample'){
            jQuery('#occ-form').show();
            jQuery('#na-occ-warn').hide();
            jQuery('#mergeSurveys').hide();
       	";
    if(!user_access($adminPerm)) {
    	data_entry_helper::$javascript .= "
			if(jQuery('#main-sample-closed').val() == '1'){
				jQuery('#read-only-survey').show();
				jQuery('#close1').hide();
				jQuery('#close2').hide();
				jQuery('#occ-form').hide(); //can't enter any more occurrences
				jQuery('#ro-sur-occ-warn').show();
				jQuery('#SurveyForm').children().attr('disabled','disabled');
    	};\n";
    } else {
    	data_entry_helper::$javascript .= "
    	    if(jQuery('#main-sample-deleted:checked').length > 0){
    	    	jQuery('#return-to-main').click();
    	    	return;
    		};\n";
    }
    data_entry_helper::$javascript .= "
			window.scroll(0,0);
            jQuery('#SurveyForm > input[name=sample\\:id]').removeAttr('disabled').val(data.outer_id);
            jQuery('#occ-form > input[name=sample\\:parent_id]').val(data.outer_id);
            jQuery('#occ-form > input[name=sample\\:date]').val(jQuery('#SurveyForm > input[name=sample\\:date]').val());
            loadAttributes('sample_attribute_value', 'sample_attribute_id', 'sample_id', data.outer_id, 'smpAttr');
            switch(\"".$args["on_save_survey_nav"]."\"){
				case \"list\":
					var a = $('ul.ui-tabs-nav a')[2];
					$(a).click();
					break;
				case \"survey\":
					break;
				default:";
    if(!user_access($adminPerm)) {
    	data_entry_helper::$javascript .= "
					if(jQuery('#main-sample-closed').val() == 0){
						var a = $('ul.ui-tabs-nav a')[1];
						$(a).click();
					};";
    } else {
    	data_entry_helper::$javascript .= "
					var a = $('ul.ui-tabs-nav a')[1];
					$(a).click();";
    }
    data_entry_helper::$javascript .= "
					break;
			}
        } else {
			if(data.error){
				var lastIndex = data.error.lastIndexOf('Validation error'); 
    			if (lastIndex != -1 && lastIndex  == (data.error.length - 16)){ 
					if(data.errors){
						// TODO translation
						for (i in data.errors){
							var label = $('<p/>').addClass('inline-error').html(data.errors[i]);
							label.insertAfter('[name='+i+']');
						}
						myScrollToError();
						return;
  					}
				}
			}
			alertIndiciaError(data);
        }
	},
    complete: function (){
  		jQuery('.loading-button').removeClass('loading-button');
  	}
});
// In this case, all the samples attributes are on the survey tab, and all the occurrence attributes are on the occurrence tab. No need to worry about getting the correct form.
loadAttributes = function(attributeTable, attributeKey, key, keyValue, prefix){
    jQuery.ajax({ 
        type: \"GET\", 
        url: \"".$svcUrl."/data/\" + attributeTable + \"?mode=json&view=list\" +
        	\"&reset_timeout=true&nonce=".$readAuth['nonce']."&auth_token=".$readAuth['auth_token']."\" +
   			\"&\" + key + \"=\" + keyValue + \"&callback=?\", 
        data: {}, 
        success: (function(attrPrefix, attrKey) {
          var retVal = function(attrdata) {
            if(!(attrdata instanceof Array)){
              alertIndiciaError(attrdata);
            } else if (attrdata.length>0) {
              for (var i=0;i<attrdata.length;i++){
                // in all cases if the attribute already has the <prefix>:<X>:<Y> format name we leave. Other wise we update <prefix>:<X> to <prefix>:<X>:<Y>
                // We leave all values unchanged.
                if (attrdata[i].id && (attrdata[i].iso == null || attrdata[i].iso == '' || attrdata[i].iso == '".$language."'))
                  jQuery('[name='+attrPrefix+'\\:'+attrdata[i][attrKey]+']').attr('name', attrPrefix+':'+attrdata[i][attrKey]+':'+attrdata[i].id)
              }
            }};
          return retVal;
          })(prefix, attributeKey),
		dataType: 'json', 
	    async: false  
	});
}";
    

    // Set up Occurrence List tab: don't include when creating a new sample as it will have no occurrences
    // Grid populated at a later point
  $r .= "<div id=\"occurrenceList\" class=\"mnhnl-btw-datapanel\"><div id=\"occ_grid\"></div>
  <form method=\"post\" action=\"".data_entry_helper::$base_url."/index.php/services/report/requestReport?report=mnhnl_btw_occurrences_report.xml&reportSource=local&auth_token=".$readAuth['auth_token']."&nonce=".$readAuth['nonce']."&mode=csv\">
    <input type=\"hidden\" id=\"params\" name=\"params\" value='{\"survey_id\":".$args['survey_id'].", \"sample_id\":".data_entry_helper::$entity_to_load['sample:id']."}' />
    <input type=\"submit\" class=\"ui-state-default ui-corner-all\" value=\"".lang::get('LANG_Download_Occurrences')."\">
  </form></div>";

  if($occReadOnly){
      // NB that we don't even include the buttons when readonly.
      data_entry_helper::$javascript .= "
jQuery('#ro-occ-occ-warn').show();
jQuery('#ro-sur-occ-warn').hide();
";
  } else {
      data_entry_helper::$javascript .= "
jQuery('#ro-occ-occ-warn').hide();
";
  }
  if($mode == 1){
      data_entry_helper::$javascript .= "jQuery('#occ-form').hide();";
  } else {
      data_entry_helper::$javascript .= "jQuery('#na-occ-warn').hide();";
  }
  // Set up Occurrence tab: don't allow entry of a new occurrence until after top level sample is saved.
  data_entry_helper::$entity_to_load=$childSample;
  $attributes = data_entry_helper::getAttributes(array(
        'id' => data_entry_helper::$entity_to_load['occurrence:id']
         ,'valuetable'=>'occurrence_attribute_value'
         ,'attrtable'=>'occurrence_attribute'
         ,'key'=>'occurrence_id'
         ,'fieldprefix'=>'occAttr'
         ,'extraParams'=>$readAuth
  ));
  $extraParams = $readAuth +
  					array('taxon_list_id' => $args['list_id'],
  						'view' => 'detail',
  						'query' => urlencode(json_encode(array('in'=>array('language_iso', array('lat', iform_lang_iso_639_2($args['language'])))))));
  if($occReadOnly){ // if the occurrence has been downloaded, no one can modify it.
      $disabledText = "disabled=\"disabled\"";
      $defAttrOptions['disabled'] = $disabledText;
  }
  $species_ctrl_args=array(
          'label'=>lang::get('LANG_Species'),
          'fieldname'=>'occurrence:taxa_taxon_list_id',
          'table'=>'taxa_taxon_list',
          'captionField'=>'taxon',
          'valueField'=>'id',
          'columns'=>2,
          'extraParams'=>$extraParams,
          'suffixTemplate'=>'requiredsuffix',
          'disabled'=>$disabledText,
          'defaultCaption' => data_entry_helper::$entity_to_load['occurrence:taxon']
  );
  $r .= "  <div id=\"occurrence\" class=\"mnhnl-btw-datapanel\">
    <p id=\"ro-occ-occ-warn\"><strong>".lang::get('LANG_Read_Only_Occurrence')."</strong></p>
    <p id=\"ro-sur-occ-warn\"><strong>".lang::get('LANG_Read_Only_Survey')."</strong></p>
    <p id=\"na-occ-warn\"><strong>".lang::get('LANG_Page_Not_Available')."</strong></p>
    <form method=\"post\" id=\"occ-form\" action=\"".iform_ajaxproxy_url($node, 'smp-occ')."\" >
    <input type=\"hidden\" id=\"website_id\" name=\"website_id\" value=\"".$args['website_id']."\" />
    <input type=\"hidden\" id=\"sample:survey_id\" name=\"sample:survey_id\" value=\"".$args['survey_id']."\" />
    <input type=\"hidden\" id=\"sample:parent_id\" name=\"sample:parent_id\" value=\"".$parentSample['sample:id']."\" />
    <input type=\"hidden\" id=\"sample:date\" name=\"sample:date\" value=\"".data_entry_helper::$entity_to_load['sample:date']."\" />
    <input type=\"hidden\" id=\"sample:id\" name=\"sample:id\" value=\"".data_entry_helper::$entity_to_load['sample:id']."\" />
    <input type=\"hidden\" id=\"occurrence:id\" name=\"occurrence:id\" value=\"".data_entry_helper::$entity_to_load['occurrence:id']."\" />
    <input type=\"hidden\" id=\"occurrence:record_status\" name=\"occurrence:record_status\" value=\"C\" />
    <input type=\"hidden\" id=\"occurrence:downloaded_flag\" name=\"occurrence:downloaded_flag\" value=\"N\" />
    ".data_entry_helper::autocomplete($species_ctrl_args)."
    ".data_entry_helper::outputAttribute($attributes[$args['occurrence_confidence_id']], array_merge($languageFilteredAttrOptions, array('noBlankText'=>'')))."
    ".data_entry_helper::sref_and_system(array('label'=>lang::get('LANG_Spatial_ref'), 'systems'=>array('2169'=>'Luref (Gauss Luxembourg)'), 'suffixTemplate'=>'requiredsuffix'))."
    <p>".lang::get('LANG_Click_on_map')."</p>
    ".data_entry_helper::outputAttribute($attributes[$args['occurrence_count_id']], array_merge($defAttrOptions, array('default'=>1, 'suffixTemplate'=>'requiredsuffix')))."
    ".data_entry_helper::outputAttribute($attributes[$args['occurrence_approximation_id']], $defAttrOptions)."
    ".data_entry_helper::outputAttribute($attributes[$args['occurrence_territorial_id']], array_merge($defAttrOptions, array('default'=>1, 'id' => 'occ-territorial')))."
    ".data_entry_helper::outputAttribute($attributes[$args['occurrence_atlas_code_id']], $languageFilteredAttrOptions)."
    ".data_entry_helper::outputAttribute($attributes[$args['occurrence_overflying_id']], $defAttrOptions)."
    ".data_entry_helper::textarea(array('label'=>lang::get('LANG_Comment'), 'fieldname'=>'occurrence:comment', 'disabled'=>$disabledText));
    if(!$surveyReadOnly && !$occReadOnly){
      if($mode == 3)
        $r .= data_entry_helper::checkbox(array('label'=>lang::get('Delete'), 'fieldname'=>'sample:deleted', 'id'=>'occ-sample-deleted'));
      $r .= "<input type=\"submit\" id=\"occ-submit\" class=\"ui-state-default ui-corner-all\" value=\"".lang::get('LANG_Save_Occurrence_Details')."\" />";
    }
    $r .= "  </form>\n";

  data_entry_helper::$javascript .= "
// because of ID tracking it is easier to rebuild entire list etc.
retriggerGrid = function(){
  $('div#occ_grid').empty();
  occListLayer.destroyFeatures();
  activateAddList = 1;
  thisOccID = -1;
  $('div#occ_grid').indiciaDataGrid('rpt:mnhnl_btw_list_occurrences', {
    indiciaSvc: '".$svcUrl."',
    dataColumns: ['taxon', 'territorial', 'count'],
    reportColumnTitles: {taxon : '".lang::get('LANG_Species')."', territorial : '".lang::get('LANG_Territorial')."', count : '".lang::get('LANG_Count')."'},
    actionColumns: {'".lang::get('LANG_Show')."' : \"".url('node/'.($node->nid), array('query' => 'occurrence_id=�id�'))."\",
            '".lang::get('LANG_Highlight')."' : \"script:highlight(�id�);\"},
    auth : { nonce : '".$readAuth['nonce']."', auth_token : '".$readAuth['auth_token']."'},
    parameters : { survey_id : '".$args['survey_id']."',
            parent_id : jQuery('#SurveyForm [name=sample\\:id]').val(),
            territorial_attr_id : '".$args['occurrence_territorial_id']."',
            count_attr_id : '".$args['occurrence_count_id']."'},
    itemsPerPage : 12,
    callback : addListFeature ,
    cssOdd : ''
  });
}

jQuery('#occ-form').ajaxForm({ 
	async: false,
	dataType:  'json', 
    beforeSubmit:   function(data, obj, options){
    	var valid = true;
    	clearErrors('form#occ-form');
    	if (!jQuery('form#occ-form > input').valid()) { valid = false; }
    	if (!jQuery('form#occ-form > select').valid()) { valid = false; }
    	if(!valid) {
			myScrollToError();
			return false;
		};
		jQuery('#occ-submit').addClass('loading-button');
		return true;
	},
    success:   function(data){
       // this will leave all the fields populated.
       	if(data.success == 'multiple records' && data.outer_table == 'sample'){
			window.scroll(0,0);
			// cant use reset form, as returns it to original values: if this was called with occurrence_id =<x> then it would repopulate with original occurrence's values
			// website_id, survey_id, record_status, downloaded_flag, sample:entered_sref_system are constants and are left alone. parent_id, date are only set referring to parent sample.
			jQuery('form#occ-form').find('[name^=occAttr\\:]').each(function(){
				var name = jQuery(this).attr('name').split(':');
				jQuery(this).attr('name', name[0]+':'+name[1]);
			});
			jQuery('form#occ-form').find('[name=occurrence\\:id],[name=sample\\:id]').val('').attr('disabled', 'disabled');
			jQuery('form#occ-form').find('[name=occurrence\\:taxa_taxon_list_id],[name=occurrence\\:taxa_taxon_list_id\\:taxon],[name=sample\\:entered_sref],[name=sample\\:geom],[name=occurrence\\:comment]').val('');
			jQuery('form#occ-form').find('[name=occAttr\\:".$args['occurrence_confidence_id']."]').find('option').removeAttr('selected');
			jQuery('form#occ-form').find('[name=occAttr\\:".$args['occurrence_count_id']."]').val('1');
			jQuery('form#occ-form').find('input[name=occAttr\\:".$args['occurrence_approximation_id']."],input[name=occAttr\\:".$args['occurrence_overflying_id']."]').removeAttr('checked','checked');
			jQuery('form#occ-form').find('#occ-territorial').attr('checked','checked');
			jQuery('label[for=occ-sample-deleted]').remove(); // sample deleted only applicable when editing an existing occurrence. After saving reverts to Add Occurreence: no delete. Remove label then actual checkbox
			jQuery('form#occ-form').find('[name=sample\\:deleted]').remove(); // This removes both parts of the checkbox.
			setAtlasStatus();
			retriggerGrid();
			locationLayer.map.editLayer.destroyFeatures();
			var a = $('ul.ui-tabs-nav a')[1];
			$(a).empty().html('<span>".lang::get('LANG_Add_Occurrence')."</span>');
			switch(\"".$args["on_save_occurrence_nav"]."\"){
				case \"list\":
					a = $('ul.ui-tabs-nav a')[2];
					$(a).click();
					break;
				case \"survey\":
					a = $('ul.ui-tabs-nav a')[0];
					$(a).click();
					break;
				default:
					break;
			}
        } else {
			if(data.error){
				var lastIndex = data.error.lastIndexOf('Validation error'); 
    			if (lastIndex != -1 && lastIndex  == (data.error.length - 16)){ 
					if(data.errors){
						// TODO translation
						for (i in data.errors){
							var label = $('<p/>').addClass('inline-error').html(data.errors[i]);
							label.insertAfter('[name='+i+']');
						}
						myScrollToError();
						return;
  					}
				}
			}
			alertIndiciaError(data);
        }
	},
    complete: function (){
  		jQuery('.loading-button').removeClass('loading-button');
  	}
});
setAtlasStatus = function() {
  if (jQuery(\"#occ-territorial:checked\").length == 0) {
      jQuery(\"select[name=occAttr\\:".$args['occurrence_atlas_code_id']."],select[name^=occAttr\\:".$args['occurrence_atlas_code_id']."\\:]\").val('');
  } else {
      if(jQuery(\"select[name=occAttr\\:".$args['occurrence_atlas_code_id']."],select[name^=occAttr\\:".$args['occurrence_atlas_code_id']."\\:]\").val() == '') {
        // Find the BB02 option (depends on the language what val it has)
        var bb02;
        jQuery.each(jQuery(\"select[name=occAttr\\:".$args['occurrence_atlas_code_id']."],select[name^=occAttr\\:".$args['occurrence_atlas_code_id']."\\:]\").find('option'), function(index, option) {
          if (option.text.substr(0,4)=='BB02') {
            bb02 = option.value;
            return; // just from the each loop
          }
        });
        jQuery(\"select[name=occAttr\\:".$args['occurrence_atlas_code_id']."],select[name^=occAttr\\:".$args['occurrence_atlas_code_id']."\\:]\").val(bb02);
      }
  }
};
jQuery(\"#occ-territorial\").change(setAtlasStatus);
if($.browser.msie) { 
    jQuery(\"#occ-territorial\").click(function() { 
        $(this).change(); 
    }); 
} 
\n";
  if($mode != 3)
    data_entry_helper::$javascript .= "setAtlasStatus();\n"; // reset the atlas when not looking at a old occurrence.
    
  $r .= '</div>';

    // add map panel.
      $options = iform_map_get_map_options($args, $readAuth);
      $olOptions = iform_map_get_ol_options($args);
      $options['layers'] = array('locationLayer', 'occListLayer');
      $options['searchLayer'] = 'false';
      $options['initialFeatureWkt'] = null;
      $options['proxy'] = '';
      $options['scroll_wheel_zoom'] = false;
      $options['width'] = 'auto'; // TBD remove from arglist
  
    $r .= "<div class=\"mnhnl-btw-mappanel\">\n";
    $r .= data_entry_helper::map_panel($options, $olOptions);
    
    // for timing reasons, all the following has to be done after the map is loaded.
    // 1) feature selector for occurrence list must have the map present to attach the control
    // 2) location placer must have the location layer populated and the map present in
    //    order to zoom the map into the location.
    // 3) occurrence list feature adder must have map present in order to zoom into any
    //    current selection.
  data_entry_helper::$onload_javascript .= "
var control = new OpenLayers.Control.SelectFeature(occListLayer);
occListLayer.map.addControl(control);
function onPopupClose(evt) {
    // 'this' is the popup.
    control.unselect(this.feature);
}
function onFeatureSelect(evt) {
    feature = evt.feature;
    popup = new OpenLayers.Popup.FramedCloud(\"featurePopup\",
               feature.geometry.getBounds().getCenterLonLat(),
                             new OpenLayers.Size(100,100),
                             feature.attributes.taxon + \" (\" + feature.attributes.count + \")\",
                             null, true, onPopupClose);
    feature.popup = popup;
    popup.feature = feature;
    feature.layer.map.addPopup(popup);
}
function onFeatureUnselect(evt) {
    feature = evt.feature;
    if (feature.popup) {
        popup.feature = null;
        feature.layer.map.removePopup(feature.popup);
        feature.popup.destroy();
        feature.popup = null;
    }
}

occListLayer.events.on({
    'featureselected': onFeatureSelect,
    'featureunselected': onFeatureUnselect
});

control.activate();

locationChange = function(obj){
  locationLayer.destroyFeatures();
  if(obj.value != ''){
    jQuery.getJSON(\"".$svcUrl."\" + \"/data/location/\"+obj.value +
      \"?mode=json&view=detail&auth_token=".$readAuth['auth_token']."&nonce=".$readAuth["nonce"]."\" +
      \"&callback=?\", function(data) {
            if (data.length>0) {
              var parser = new OpenLayers.Format.WKT();
              for (var i=0;i<data.length;i++)
        {
          if(data[i].centroid_geom){
            ".self::readBoundaryJs('data[i].centroid_geom', $args['map_projection'])."
            feature.style = {label: data[i].name,
						     strokeColor: \"Green\",
                             strokeWidth: 2,
                             fillOpacity: 0};
            centre = feature.geometry.getCentroid();
            centrefeature = new OpenLayers.Feature.Vector(centre, {}, {label: data[i].name});
            locationLayer.addFeatures([feature, centrefeature]);
          }
          if(data[i].boundary_geom){
            ".self::readBoundaryJs('data[i].boundary_geom', $args['map_projection'])."
            feature.style = {strokeColor: \"Blue\", strokeWidth: 2};
            locationLayer.addFeatures([feature]);
          }
          locationLayer.map.zoomToExtent(locationLayer.getDataExtent());
        }
      }
    });
     jQuery.getJSON(\"".$svcUrl."\" + \"/data/location\" +
      \"?mode=json&view=detail&auth_token=".$readAuth['auth_token']."&nonce=".$readAuth["nonce"]."&callback=?&parent_id=\"+obj.value, function(data) {
            if (data.length>0) {
              var parser = new OpenLayers.Format.WKT();
              for (var i=0;i<data.length;i++)
        {
          if(data[i].centroid_geom){
            ".self::readBoundaryJs('data[i].centroid_geom', $args['map_projection'])."
            locationLayer.addFeatures([feature]);
          }
          if(data[i].boundary_geom){
            ".self::readBoundaryJs('data[i].boundary_geom', $args['map_projection'])."
            feature.style = {label: data[i].name,
              labelAlign: \"cb\",
              strokeColor: \"Blue\",
                        strokeWidth: 2};
            locationLayer.addFeatures([feature]);
           }
         }
      }
        });
  }
};
// upload location initial value into map.
jQuery('#imp-location').each(function(){
  locationChange(this);
});
jQuery('#imp-location').unbind('change');
jQuery('#imp-location').change(function(){
  locationChange(this);
});
var selected = $('#controls').tabs('option', 'selected');

// Only leave the click control activated for edit/add occurrence tab.
if(selected != 1){
    locationLayer.map.editLayer.clickControl.deactivate();
}
$('#controls').bind('tabsshow', function(event, ui) {
        if(ui.index == 1)
        {
         locationLayer.map.editLayer.clickControl.activate();
        }
        else
        {
         locationLayer.map.editLayer.clickControl.deactivate();
        }
    }
);
activateAddList = 1;
thisOccID = ".$thisOccID.";
addListFeature = function(div, r, record, count) {
  if(activateAddList == 0)
    return;
  if(r == count)
    activateAddList = 0;
    var parser = new OpenLayers.Format.WKT();
    ".self::readBoundaryJs('record.geom', $args['map_projection'])."
    if(record.id != thisOccID || 1==".($surveyReadOnly ? 1 : 0)." || 1==".($occReadOnly ? 1 : 0)."){
      feature.attributes.id = record.id;
      feature.attributes.taxon = record.taxon;
      feature.attributes.count = record.count;
      occListLayer.addFeatures([feature]);
      if(record.id == ".$thisOccID."){
        var bounds=feature.geometry.getBounds();
        locationLayer.map.setCenter(bounds.getCenterLonLat());
      }
    } else {
      locationLayer.map.editLayer.destroyFeatures();
      locationLayer.map.editLayer.addFeatures([feature]);
      var bounds=feature.geometry.getBounds()
      var centre=bounds.getCenterLonLat();
      locationLayer.map.setCenter(centre);
    }
};
highlight = function(id){
  if(id == ".$thisOccID."){
    if(occListLayer.map.editLayer.features.length > 0){
      var bounds=occListLayer.map.editLayer.features[0].geometry.getBounds()
      var centre=bounds.getCenterLonLat();
      occListLayer.map.setCenter(centre);
      return;
    }
  }
  for(var i = 0; i < occListLayer.features.length; i++){
    if(occListLayer.features[i].attributes.id == id){
      control.unselectAll();
      var bounds=occListLayer.features[i].geometry.getBounds()
      var centre=bounds.getCenterLonLat();
      occListLayer.map.setCenter(centre);
      control.select(occListLayer.features[i]);
      return;
    }
  }
}
";
  if($mode != 1){
    data_entry_helper::$onload_javascript .= "
$('div#occ_grid').indiciaDataGrid('rpt:mnhnl_btw_list_occurrences', {
    indiciaSvc: '".$svcUrl."',
    dataColumns: ['taxon', 'territorial', 'count'],
    reportColumnTitles: {taxon : '".lang::get('LANG_Species')."', territorial : '".lang::get('LANG_Territorial')."', count : '".lang::get('LANG_Count')."'},
    actionColumns: {'".lang::get('LANG_Show')."' : \"".url('node/'.($node->nid), array('query' => 'occurrence_id=�id�'))."\",
            '".lang::get('LANG_Highlight')."' : \"script:highlight(�id�);\"},
    auth : { nonce : '".$readAuth['nonce']."', auth_token : '".$readAuth['auth_token']."'},
    parameters : { survey_id : '".$args['survey_id']."',
            parent_id : '".$parentSample['sample:id']."',
            territorial_attr_id : '".$args['occurrence_territorial_id']."',
            count_attr_id : '".$args['occurrence_count_id']."'},
    itemsPerPage : 12,
    callback : addListFeature ,
    cssOdd : ''
  });

// activateAddList = 0;

";
    };
    $r .= "</div><div><form><input id=\"return-to-main\" type=\"button\" value=\"".lang::get('LANG_Return')."\" onclick=\"window.location.href='".url('node/'.($node->nid), array('query' => 'Main'))."'\"></form></div></div>\n";

    return $r;
  }

  /**
   * Handles the construction of a submission array from a set of form values.
   * @param array $values Associative array of form data values.
   * @param array $args iform parameters.
   * @return array Submission structure.
   */
  public static function get_submission($values, $args) {
    // All done using AJAX.
    return(null);
  }

  /**
   * Retrieves a list of the css files that this form requires in addition to the standard
   * Drupal, theme or Indicia ones.
   *
   * @return array List of css files to include for this form.
   */
  public static function get_css() {
    return array('mnhnl_bird_transect_walks.css');
  }

  /**
   * Construct JavaScript to read and transform a boundary from the supplied
   * object name.
   * @param string $name Name of the existing geometry object to read the feature from.
   * @param string $proj EPSG code for the projection we want the feature in.
   */
  private static function readBoundaryJs($name, $proj) {
    $r = "feature = parser.read($name);";
	if ($proj!='900913') {
	  $r .= "\n    feature.geometry.transform(new OpenLayers.Projection('EPSG:900913'), new OpenLayers.Projection('EPSG:" . $proj . "'));";
    }
    return $r;
  }
}