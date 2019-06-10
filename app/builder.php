<?php
	/**
	 * E-signature builder
	 * v1.2
	 * (C)2018-2019 Gareth 'GazChap' Griffiths
	 */

	require dirname(__FILE__) . "/../build_config.php";

	$dataColumnsKeys = array_keys( $dataColumns );
	$dataColumnsValues = array_values( $dataColumns );

	if ( $signaturefile = fopen( $dataFile, "rb")) {

		if ( !is_dir( $outputFolder ) ) {
			mkdir( $outputFolder ) ;
		}

		$counter = 0;
		while ( $data = @fgetcsv( $signaturefile, 1024, "\t" ) ) {
			if ( !$dataHasHeaderRow || $counter ) {
				$data = array_map( 'trim', $data );

				if ( !empty( $data ) ) {
					$embedImages = true; // using the embedded image names from Outlook is the default behaviour

					$person = array();
					$outputName = "";
					foreach( $dataColumnsKeys as $i => $key ) {
						if ( isset( $data[$i] ) ) {
							$v = $data[$i];
						} else {
							$v = "";
						}

						switch( $key ) {
							case "[full_name]":
								if ( isset( $dataColumns['forenames'] ) && isset( $dataColumns['surname'] ) ) {
									$forenames = $data[ array_search( 'forenames', $dataColumnsKeys ) ];
									$surname = $data[ array_search( 'surname', $dataColumnsKeys ) ];
									$person[ $key ] = trim( $forenames . " " . $surname );

									$outputName = $person[ $key ];
								}
								break;

							case "web_images":
								if ( !empty( $v ) ) {
									$embedImages = false;
								}
								break;
							default:
								$person[ $key ] = $v;
						}
						if ( !empty( $dataColumnsValues[ $i ] ) && isset( $person[ $key ] ) ) {
							$person[ $dataColumnsValues[ $i ] ] = $person[ $key ];
						}
					}

					if ( !$outputName ) {
						if ( !empty( $person[ 'forenames' ] ) ) {
							$outputName = $person['forenames'];
						} else {
							$outputName = $person[ $dataColumnKeys[0] ];
						}
					}


					foreach( $templateList as $templateName ) {
						if ( empty( $person['[template]'] ) || $person['[template]'] == $templateName ) {
							$outputTemplateName = replace_placeholders( $templateName, $person );
							if ( !$quietMode ) {
								echo "Input Template: " . $templateName . "\r\n";
								echo "Output Template: " . $outputTemplateName . "\r\n";
							}
							$files_src_dir = $templateFolder . $templateName . "_files";
							$files_dest_dir = replace_placeholders( $outputFolder . $outputTemplateName . "_files", $person );

							// copy template folder
							xcopy($files_src_dir, $files_dest_dir);
							$filenames = array(
								$templateFolder . $templateName . ".htm",
								$templateFolder . $templateName . ".txt",
								$templateFolder . $templateName . ".rtf",
							);

							foreach($filenames as $i => $filename) {
								$ext = strrchr( $filename, "." );
								$output_filename = $outputFolder . $outputTemplateName . $ext;

								if ( file_exists( $filename ) ) {
									$contents = file_get_contents( $filename );
									if ( $ext == ".htm" ) {
										$contents = str_replace( "\r\n", " ", $contents );
									}
									$contents = replace_placeholders( $contents, $person );

									if ( !$embedImages && stristr( $filename, ".htm" ) ) {
										foreach( $imageUrls as $embeddedName => $imageUrl ) {
											$contents = str_replace('src="' . rawurlencode($templateName) . '_files/' . $embeddedName, 'src="' . $imagesBaseUrl . $imageUrl . '"', $contents);
										}
									} else {
										$contents = str_replace(rawurlencode($templateName) . '_files', rawurlencode($outputTemplateName) . '_files', $contents);
									}

									if ($fp = fopen($output_filename, "wb")) {
										fputs( $fp, $contents );
										fclose( $fp );
									}

									if ( !$quietMode ) {
										if ( !$i ) echo "Output format: ";
										echo $ext . " ";
									}
								}
							}
						}
					}
				}

				if ( !$quietMode ) echo "[All OK!]\r\n\r\n";
			}
			$counter++;
		}
		fclose($signaturefile);
	}

	function xcopy($source, $dest){
		// Simple copy for a file
		if (is_file($source)) {
			return copy($source, $dest);
		}

		// Make destination directory
		if (!is_dir($dest)) {
			mkdir($dest);
		}

		// Loop through the folder
		$dir = dir($source);
		while (false !== $entry = $dir->read()) {
			// Skip pointers
			if ($entry == '.' || $entry == '..') {
				continue;
			}

			// Deep copy directories
			if ($dest !== "$source/$entry") {
				xcopy("$source/$entry", "$dest/$entry");
			}
		}

		// Clean up
		$dir->close();
		return true;
	}

	function replace_placeholders( $string, $data ) {
		foreach($data as $key => $value) {
			$string = str_replace("[[" . $key . "]]", $value, $string);
			$string = str_replace("[[" . $key . "_RAWURLENCODED]]", rawurlencode( $value ), $string);

			$string = str_replace("%5b%5b" . $key . "%5d%5d", $value, $string);
			$string = str_replace("%5b%5b" . $key . "_RAWURLENCODED%5d%5d", rawurlencode( $value ), $string);
		}

		if ( stristr( $string, '[[IF' ) || stristr( $string, '[[ENDIF]]' ) ) {
			$string = preg_replace("/\[\[IF (.*?)\]\]/i", "<?php if (\"\\1\"):?>", $string);
			$string = preg_replace("/\[\[ENDIF\]\]/i", "<?php endif;?>", $string);
			ob_start();
			eval("?>" . $string . "<?php ");
			$string = ob_get_clean();
		}

		return $string;
	}
