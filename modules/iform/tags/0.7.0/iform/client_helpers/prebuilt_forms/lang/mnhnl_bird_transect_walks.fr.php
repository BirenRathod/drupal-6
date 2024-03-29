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
 * @author	Indicia Team
 * @license	http://www.gnu.org/licenses/gpl.html GPL 3.0
 * @link 	http://code.google.com/p/indicia/
 */

global $custom_terms;

/**
 * Language terms for the survey_reporting_form_2 form.
 *
 * @package	Client
 */
$custom_terms = array(
	'LANG_not_logged_in' => 'Vous devez vous inscrire pour voir ce contenu.',
	'LANG_Location_Layer' => 'Couche des transects',
	'LANG_Occurrence_List_Layer'=> 'Couche des contacts',
	'LANG_Surveys' => 'Inventaires',
	'LANG_Allocate_Locations' => 'Allocate Locations',
	'LANG_Transect' => 'Transect',
	'LANG_Date' => 'Date',
	'LANG_Visit_No' => 'Passage No',
	'LANG_Num_Occurrences' => '# Contacts',
	'LANG_Num_Species' => '# Esp�ces',
	'LANG_Show' => 'Editer',
	'LANG_Add_Survey' => 'Ajouter nouveau �chantillonnage',
	'LANG_Not_Allocated' => 'Not Allocated',
	'LANG_Save_Location_Allocations' => 'Save Location Allocations',
	'LANG_Survey' => 'Echantillonage',
	'LANG_Show_Occurrence' => 'Montrer contact',
	'LANG_Edit_Occurrence' => 'Editer contact',
	'LANG_Add_Occurrence' => 'Ajouter contact',
	'LANG_Occurrence_List' => 'Liste des contacts',
	'LANG_Read_Only_Survey' => 'Cet �chantillonnage est bloqu�.',
	'LANG_Read_Only_Occurrence' => 'Ce contact a �t� t�l�charg� et est prot�g� maintenant.',
	'LANG_Save_Survey_Details' => 'Enregistrer �chantillonage',
	'LANG_Save_Survey_And_Close' => 'Enregistrer et fermer �chantillonnage',
	'LANG_Close_Survey_Confirm' => '�tes-vous s�re de vouloir fermer cet �chantillonage? Les donn�es de cet �chantillonnage seront enregistr�es et prot�g�es, � la suite vous ne pouvez plus les �diter.',
	'LANG_Species' => 'Esp�ce',
	'LANG_Spatial_ref' => 'Coordonn�es spatiales',
	'LANG_Click_on_map' => 'Cliquez sur la carte pour attribuer les coordonn�es spatiales',
	'LANG_Comment' => 'Commentaires',
	'LANG_Save_Occurrence_Details' => 'Sauvegarder contact',
	'LANG_Territorial' => 'Territorial',
	'LANG_Count' => 'Nombre',
	'LANG_Highlight' => 'En �vidence',
	'LANG_Download' => 'Reports and Downloads',
	'LANG_Direction_Report' => 'Run a report to check that all non downloaded closed surveys have been walked in the same direction as the previously entered survey on that location. Returns the surveys which are in a different direction.',
	'LANG_Direction_Report_Button' => 'Run Survey Direction Warning Report - CSV',
	'LANG_Initial_Download' => 'Carry out initial download of closed surveys. Sweeps up all records which are in closed surveys but which have not been downloaded yet',
    'LANG_Initial_Download_Button' => 'Initial Download - CSV',
	'LANG_Confirm_Download' => 'Carry out confirmation download. This outputs the same data that will be included in the final download, but does not tag the data as downloaded. Only includes data in the last initial download unless a survey has since been reopened, when it will be excluded from this report.',
    'LANG_Confirm_Download_Button' => 'Confirmation Download - CSV',
	'LANG_Final_Download' => 'Carry out final download. Data will be marked as downloaded and no longer editable.',
    'LANG_Final_Download_Button' => 'Final Download - CSV',
	'LANG_Download_Occurrences' => 'T�l�chargement de la liste des contacts (format CSV)',
	'LANG_No_Access_To_Location' => 'Le transect pour lequel cet �chantillonnage a �t� effectu� ne vous est pas attribu� - vous ne pouvez acc�der les informations de ce contact.',
	'LANG_No_Access_To_Sample' => 'This record is not a valid top level sample.',
	'LANG_Page_Not_Available' => 'Cette page est non-disponible actuellement.',
	'LANG_Return' => 'Retour � l\'�cran principal des �chantillonnages',
	'validation_required' => 'Veuillez entrer une valeur pour ce champ',

	'LANG_Error_When_Moving_Sample' => 'An error has occurred during the merge process. Failed to move an occurrence.',
	'LANG_Error_When_Deleting_Sample' => 'An error has occurred during the merge process. Failed to delete empty survey.',
	'LANG_Found_Mergable_Surveys' => 'A number of surveys have been found which share the same transect and date combination as this one.',
	'LANG_Merge_With_ID' => 'Merge this survey with id',
	'LANG_Indicia_Warehouse_Error' => 'Error returned from Indicia Warehouse',
	'LANG_Survey_Already_Exists' => 'Un �chantillonnage existe d�j� pour cette combinaison de transecte/date. �tes-vous s�re de vouloir rajouter/sauvegarder celle-ci?',
	'LANG_No_Access_To_Occurrence' => 'This record is not a valid occurrence.',

	// Can also add entries for 'Yes' and 'No' for the boolean attributes,
	//   and one for each of the attribute captions. As these are in English
	//   they are omitted from this file. Note these do not have LANG_ prefixes.



	'Atlas Code' => 'Code Atlas',
	'Approximation?' => 'Nombre approxim�?',
	'Overflying' => 'Survolant',
	'Closed' => 'Ferm�',
	'Cloud Cover' => 'Couverture nuageuse',
	'Confidence' => 'Certitude',
	'Count' => 'Nombre',
	'End Time' => 'Heure d\'arriv�e',
	'No' => 'Non',
	'validation_required' => 'Veuillez entrer une valeur pour ce champ',
	'Precipitation' => 'Pr�cipitations',
	'Reliability of this data' => 'Fiabilit� des donn�es',
	'Start Time' => 'Heure de d�part',
	'Temperature (Celsius)' => 'Temp�rature (Celsius)',
	'Territorial' => 'Territorial',
	'Visit number in year' => 'Num�ro de passage',
	'Walk started at end' => 'Parcours',
	'Wind Force' => 'Force du vent',
	'Yes' => 'Oui'


);