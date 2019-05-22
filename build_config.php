<?php
	/**
	 * E-signature builder - configuration file
	 */

	/**
	 * An array of the templates in the Templates folder
	 * This should be the "basename" of the .htm file, which should match the folder name before the "_files" part
	 */
	$templateList = array(
		'Example - [[NAME]]',
	);

	/**
	 * Columns in the names file, and their associated placeholder tag (without the surrounding [[ ]])
	 * The keys of the array must be in the order they appear in the file (as columns)
	 * If any column is always empty or not to be used, leave the placeholder tag blank
	 *
	 * Special keys:
	 * web_images: If set, will tell the builder to replace embedded image references with online equivalents based on $imagesBaseUrl and $imageUrls below.
	 * [full_name]: note the [] surround - if this exists, it will look for 'forenames' and 'surname' and concatenate them together with a space between them
	 */
	$dataColumns = array(
		'name'		=> 'NAME',
		'title'		=> 'JOBTITLE',
		'email'		=> 'EMAIL',
		'tel'		=> 'TEL',
		'mobile'	=> 'MOBILE',
	);

	/**
	 * The filename of the data to use for the builder, relative to the $baseFolder defined further down
	 */
	$dataFile = "data.txt";

	/**
	 * For email signatures where the images are pulled from the web rather than embedded, use this as a base URL
	 * Don't forget the trailing slash!
	 */
	$imagesBaseUrl = "https://www.example.com/";

	/**
	 * Array of embedded image filename translations.
	 * Key should be the filename that Outlook assigns in the *_files folder
	 * Value should be the filename on the server in the $imagesBaseUrl above.
	 *
	 * An empty array here means it won't translate image names, and you just need to upload image001.png etc. to the $imagesBaseUrl folder
	 */
	$imageUrls = array(
		'image001.png'	=> 'logo.png',
	);

	/***** MOST OF THE TIME YOU WON'T NEED TO CHANGE ANYTHING BELOW THIS LINE *****/

	/**
	 * Enable quiet mode, which will just silently produce all the signatures and not warn of errors etc.
	 */
	$quietMode = false;

	/**
	 * General path setup - you should never really need to change these
	 */
	$baseFolder = dirname(__FILE__) . "/files/";
	$templateFolder = $baseFolder . "templates/";
	$outputFolder = $baseFolder . "output/";

	/**
	 * The filename of the file containing the names etc. for the signatures to be built from
	 */
	$dataFile = $baseFolder . $dataFile;

	/**
	 * Should we ignore the first row because it has headers in? Most of the time you'll leave this true
	 */
	$dataHasHeaderRow = true;