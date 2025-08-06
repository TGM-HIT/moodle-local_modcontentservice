# Module Content Service

This plugin provides an [External Service](https://moodledev.io/docs/5.0/apis/subsystems/external) for updating module contents. It supports the following modules and fields:

- [`mod_assign`](https://docs.moodle.org/500/en/Assignment_activity): Description, Activity instructions, Additional files
- [`mod_folder`](https://docs.moodle.org/500/en/Folder_resource): Description, Files
- [`mod_page`](https://docs.moodle.org/500/en/Page_resource): Description, Page content
- [`mod_resource`](https://docs.moodle.org/500/en/File_resource): Description, Files

In addition, updating a module through this plugin updates the revision number (except for `mod_assign`, which doesn't use revision numbers) and modification timestamp.

The rich text fields (description, activity instructions, page content) support attached files (usually images). Naturally, the file fields do too. When referencing an uploaded file in rich text, the path must be referred to as `@@PLUGINFILE@@/<filename>`. Here, `@@PLUGINFILE@@` is a prefix that Moodle normally inserts when post-processing form input.

## Example usage

This example requires the following prerequisites:

- the plugin is installed on the Moodle server `http://localhost:8000/`
- the service `modcontentservice` is activated
- there is a user with permission to use the service
- you have the web service token for that user (these three steps can be accomplished by the `cli/webservicesetup.php` script)
- you have an assignment activity with ID=2, i.e. its URL is http://localhost:8000/mod/assign/view.php?id=2
- the user has permission to edit this activity, i.e. is a teacher in the assignment's course

If all of these are fulfilled, you should be able to use the following script:

```bash
TOKEN='YOUR-WEBSERVICE-TOKEN'
BASE_URL='http://localhost:8000'
SERVICE_URL="$BASE_URL/webservice/rest/server.php?wstoken=$TOKEN"
UPLOAD_URL="$BASE_URL/webservice/upload.php?token=$TOKEN"

# create an example file to upload
echo "An attachment" > example.txt
# upload the file and save the item ID
ITEMID=$(
    curl -X POST \
    -F 'file_1=@example.txt' \
    "$UPLOAD_URL" \
    | jq '.[0]["itemid"]')
```

This piece calls a standard Moodle endpoint for [file upload](https://moodledev.io/docs/5.0/apis/subsystems/external/files#file-upload). The endpoint returns, among other things, the "item id" of the uploaded file. Multiple files can go in the same file area by specifying a previous item ID on a subsequent upload, e.g. when embedding multiple images, attaching multiple files, or filling a folder.

After this is done, the plugin's endpoint is called:

```bash
curl -X POST \
    -F "cmid=2" \
    -F "intro[text]=Hello" \
    -F "intro[format]=1" \
    -F "activity[text]=World" \
    -F "activity[format]=1" \
    -F "attachments=$ITEMID" \
    "$SERVICE_URL&wsfunction=local_modcontentservice_update_assign_content"
```

The `cmid` parameter is the ID of the activity. Since it's an assignment, the function we call is `local_modcontentservice_update_assign_content`. The `intro[format]` and `activity[format]` parameters are specified as 1, which means HTML; this is already the default, so it's not necessary to specify them. `attachments` is optional if none are needed.

To embed an image in the assignment description, you would first upload e.g. `example.jpg` and specify `intro[itemid]=$ITEMID`. In the `intro[text]` content, you could then insert e.g. `<img src="@@PLUGINFILE@@/example.jpg" />`.

### Python example usage

The same can for example be achieved using the [moodlepy](https://pypi.org/project/moodlepy/) library for Python. Due to a bug around form argument parsing, and because it supports file uploads natively, I recommend moodlepy as a git dependency on the [`improvements` branch of my fork](https://github.com/SillyFreak/moodlepy/tree/improvements), e.g. using

```bash
pip install git+https://github.com/SillyFreak/moodlepy@improvements
```

Assuming the same prerequisites as before, plus an existing example file, this script will achieve the same thing:

```py
from moodle import Moodle

token = 'YOUR-WEBSERVICE-TOKEN'
url = 'http://localhost:8000/webservice/rest/server.php'

moodle = Moodle(url, token)

itemid = moodle.upload(
    ('example.txt', open('./example.txt', 'rb')),
)[0].itemid

result = moodle.post(
    "local_modcontentservice_update_assign_content",
    cmid=2,
    intro=dict(
        text="Hello",
        format=1,
    ),
    activity=dict(
        text="World",
        format=1,
    ),
    attachments=itemid,
)
print(result)
```

### Python CLI example usage

You can use the [Moodle CLI](https://github.com/TGM-HIT/moodle-cli?tab=readme-ov-file#example-usage) tool to conveniently use this web service. See the repo's documentation for details.

## Endpoint functions

All endpoints live in the `local_modcontentservice` namespace and are named `update_..._content`, where `...` is one of the supported module names (`assign`, `folder`, `page`, `resource`). The exact parameters are as follows:

- `update_assign_content`:
  - `cmid`: the module ID; `int`, required
  - `intro`: the description; rich text (see below), required
  - `activity`: the activity instructions; rich text, required
  - `attachments`: the attachment file item ID; `int`, optional (default: no files)

- `update_folder_content`:
  - `cmid`: the module ID; `int`, required
  - `intro`: the description; rich text, required
  - `files`: the folder file item ID; `int`, required

- `update_page_content`:
  - `cmid`: the module ID; `int`, required
  - `intro`: the description; rich text, required
  - `page`: the page content; rich text, required

- `update_resource_content`:
  - `cmid`: the module ID; `int`, required
  - `intro`: the description; rich text, required
  - `files`: the file item ID; `int`, required

File item IDs are obtained from calling Moodle's [file upload](https://moodledev.io/docs/5.0/apis/subsystems/external/files#file-upload) endpoint. All endpoints simply return `"ok"` on success; this will be improved. Rich text parameters are passed as multiple keys. For example, an `intro` rich text parameter contains

- `intro[text]`: the actual text; `string`, required. To embed images, the URL must be written as `src="@@PLUGINFILE@@/<filename>"`. Moodle will replace `@@PLUGINFILE@@` with the correct URL prefix for the module being updated.
- `intro[format]`: the text format; `int`, optional (default: 1 for HTML; other options: 0 for Moodle, 2 for plain text, 4 for Markdown)
- `intro[itemid]`: the file item ID for embedded images; `int`, optional (default: no files)

## Installing via uploaded ZIP file

1. Log in to your Moodle site as an admin and go to _Site administration >
   Plugins > Install plugins_.
2. Upload the ZIP file with the plugin code. You should only be prompted to add
   extra details if your plugin type is not automatically detected.
3. Check the plugin validation report and finish the installation.

## Installing manually

The plugin can be also installed by putting the contents of this directory to

```
{your/moodle/dirroot}/local/modcontentservice
```

Afterwards, log in to your Moodle site as an admin and go to _Site administration >
Notifications_ to complete the installation.

Alternatively, you can run

```bash
php admin/cli/upgrade.php
```

to complete the installation from the command line.

## License

2025 Clemens Koza <ckoza@tgm.ac.at>

This program is free software: you can redistribute it and/or modify it under
the terms of the GNU General Public License as published by the Free Software
Foundation, either version 3 of the License, or (at your option) any later
version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY
WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
PARTICULAR PURPOSE.  See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with
this program.  If not, see <https://www.gnu.org/licenses/>.
