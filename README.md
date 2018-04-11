# GazChap's Email Signature Builder

## What does this thing do?

Simple. It takes a series of template files (usually .htm and .txt) and replaces a set of placeholders with data taken from another file.

I use it to quickly build a suite of email signatures for Outlook and other email clients based on a template, but all it's doing is string substitution so with some modifications, there's really no reason why it can't be used for any similar purpose.

## How do I use it?

You'll need:

* PHP, preferably the CLI version
* An email signature template (or whatever you want to build copies of)
* A tab-delimited text file that represents your data

Included in this repo is a basic Windows/MS-DOS batch file (`run.bat`) which literally just runs the PHP-CLI to run the builder script.

## Configuring the builder

The main code for the builder lives in the app directory, and you shouldn't need to touch this.

To configure the builder, there's a `build_config.php` file that you can edit to control various elements, namely:

Variable | Description
-------- | -----------
`$quietMode` | false by default. If set to true, all output from the script will be suppressed except any PHP errors.
`$baseFolder` | the folder that all other folder names are based on, defaults to the `files` folder in this repo.
`$templateFolder` | where, relative to `$baseFolder`, are the actual template files kept.
`$outputFolder` | where, relative to `$baseFolder`, should the outputted files be saved.
`$templateList` | an array of template name "stubs". Outlook saves email signatures as .htm, .rtf and .txt files and then any files are kept in a "_files" folder, e.g. `Example.htm` would have an `Example_files` folder. The "stub" used for this example should be `Example`.
`$dataFile` | where, relative to `$baseFolder`, should the script look for the tab-delimited text file to be used when substituting.
`$dataColumns` | an array that describes the contents of the `$dataFile` - the key of each array item can be anything really (with a couple of exceptions below), but the value should be the placeholder that will be substituted in the file (read about placeholders and special columns below)
`$dataHasHeaderRow` | true by default. If true, the builder will skip over the first row in `$dataFile`
`$imagesBaseUrl` | If the "web_images" column is present in the file, any images used in the signature should be uploaded to a remote server in a single folder, and you can then tell the builder to replace the "embedded URL" that Outlook creates with a web-based one, with this as the base.
`$imageUrls` | an array that maps the "embedded filenames" from Outlook to images on the remote server used by `$imagesBaseUrl`. The key of each element should be the embedded filename, e.g. `image001.png` and the value should be the path to it, relative to `$imagesBaseUrl`, e.g. `logo.png`

## Special columns

There are a couple of special array keys that can be specified for `$dataColumns`:

Key | Purpose
--- | -------
`web_images` | With this column, if any rows have this column filled in, the embedded URLs for files used in the signature template will be replaced by the image URL as built up by the `$imagesBaseUrl` and `$imageUrls` variables above.
`[full_name]` | Note the square brackets, these are required. If this is present, and there are keys in the `$dataColumns` array for `forenames` and `surname`, they will be concatenated together and used for this column.

## Placeholders

To perform the substitutions, you need to put placeholders into the template files.

Placeholders are just the values of each element in the `$dataColumns` array, surrounded by two square brackets on either side. For example, `NAME` becomes `[[NAME]]`

Wherever the placeholder appears in the template file (and also in the template "stub"!) it will be replaced - so you can use, for example, `[[NAME]]` in the template filename to have it create a template for each user in your data file.

There is also a special placeholder: `[[IF]] ... [[ENDIF]]`

This allows you to hide certain parts of your template based on whether the data exists for that row or not. To only show a telephone number if the `TEL` column is filled out, you would do something like this: `[[IF [[TEL]] ]] T: [[TEL]] [[ENDIF]]`

## Disclaimer

I use this for my own purposes, and make no claims that it will a) do what *you* want it to do, nor that b) it won't screw anything up on your machine.

I can take no responsibility for anything that you do with this, or any damage that it does to your files, computer, you, your pets or anything like that.

Basically, you're on your own...