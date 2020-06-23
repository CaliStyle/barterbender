# Photos  :: Change Log

## Version 4.7.7 ##

### Information ###

- **Release Date:** September 05, 2019
- **Best Compatibility:** phpFox >= 4.7.8

### Bug fixed

- Show wrong photos count in meta description [#2737](https://github.com/PHPfox-Official/phpfox-v4-issues/issues/2737).
- Weird little bug in photo link area / private mode [#2743] (https://github.com/PHPfox-Official/phpfox-v4-issues/issues/2743)

### Changed files ###

- M Block/FeaturedAlbumBlock.php
- M Block/SponsoredAlbumBlock.php
- M Controller/IndexController.php
- M Install.php
- M Service/Album/Album.php
- M Service/Callback.php
- M assets/main.less
- M phrase.json
- M views/controller/edit-album.html.php
- M views/controller/view.html.php


## Version 4.7.6 ##

### Information ###

- **Release Date:** July 15, 2019
- **Best Compatibility:** phpFox >= 4.7.7

### Improvements ###

- Show feed status with video in the wall of the person tagged/mentioned.

### Bug fixed

- Layout issue.

### Changed files ###

- M Ajax/Ajax.php
- M Block/EditPhoto.php
- M Controller/AddController.php
- M Controller/FrameDragDropController.php
- M Controller/IndexController.php
- M Install.php
- M README.md
- M Service/Browse.php
- M Service/Callback.php
- M Service/Photo.php
- M Service/Process.php
- M assets/main.less
- M changelog.md
- M phrase.json
- M views/block/form.html.php
- M views/block/mass-edit-item.html.php
- M views/block/photo_entry.html.php
- M views/controller/add.html.php
- M views/controller/albums.html.php
- M views/controller/tag.html.php
- M views/controller/upload.html.php
- M views/controller/view.html.php


## Version 4.7.5 ##

### Information ###

- **Release Date:** May 24, 2019
- **Best Compatibility:** phpFox >= 4.7.5

### Improvements ###

- Support user can drag/drop to reorder photos in the edit album page [#2627](https://github.com/PHPfox-Official/phpfox-v4-issues/issues/2627)
- Support user can sort by the title of photo (A-Z, Z-A) in photos page (All Photos, My Photos, Friends' Photos, User's Photos, Page's Photos, and Group's Photos)[#2627](https://github.com/PHPfox-Official/phpfox-v4-issues/issues/2627)
- View photo - User could able to use next-pre on keyboard to slide next photo.

### Bug fixed

- Issue with mobile css dimming entire photos [#2626](https://github.com/PHPfox-Official/phpfox-v4-issues/issues/2626)

### Changed files ###

- M Ajax/Ajax.php
- M Controller/AlbumController.php
- M Controller/AlbumsController.php
- M Controller/EditAlbumController.php
- M Controller/FrameDragDropController.php
- M Controller/IndexController.php
- M Install.php
- M README.md
- M assets/autoload.js
- M assets/main.less
- M changelog.md
- M phrase.json
- M views/block/edit-photo.html.php
- M views/controller/album.html.php
- M views/controller/edit-album.html.php
- M views/controller/view.html.php


## Version 4.7.4 ##

### Information ###

- **Release Date:** April 10, 2019
- **Best Compatibility:** phpFox >= 4.7.5

### Improvements ###

- Support select photo mode views.

### Bug fixed

- Feed - Showing wrong phrases.
- Issue when Sponsor album with payment and auto publish after sponsored.
- Show pending photos in User profile >>Photos.

### Changed files ###

- M Ajax/Ajax.php
- M Controller/AlbumController.php
- M Controller/DownloadController.php
- M Controller/FrameDragDropController.php
- M Controller/IndexController.php
- M Controller/ViewController.php
- M Install.php
- M README.md
- M Service/Album/Album.php
- M Service/Api.php
- M Service/Callback.php
- M Service/Process.php
- M assets/autoload.js
- M changelog.md
- M views/block/drop-down.html.php
- M views/block/mass-edit-item.html.php
- M views/controller/album.html.php
- M views/controller/index.html.php


### New Settings

| ID | Var name | Name | Description |
| --- | -------- | ---- | --- |
| 1 | photo_mode_views | Photo Mode Views | Select mode views for photos |


## Version 4.7.3 ##

### Information ###

- **Release Date:** March 04, 2019
- **Best Compatibility:** phpFox >= 4.7.4

### Improvements ###

- Support editing status feeds with Photo.

### Bug fixed

- Feed - Showing wrong phrases.
- Issue when Sponsor album with payment and auto publish after sponsored.
- Show pending photos in User profile >>Photos.

### Changed files ###

- M Block/SponsoredAlbumBlock.php
- M Install.php
- M README.md
- M Service/Callback.php
- M changelog.md
- M Service/Album/Process.php
- M Ajax/Ajax.php
- M Controller/IndexController.php

## Version 4.7.2

### Information

- **Release Date:** January 23, 2019
- **Best Compatibility:** phpFox >= 4.7.1

### Fixed Bugs

- Photo detail: Comment count is working incorrectly when deleting a comment.

## Improvements

- Compatible with Core 4.7.3

### Changed Files

- M Ajax/Ajax.php
- M Block/SponsoredAlbumBlock.php
- M Controller/FrameDragDropController.php
- M Install.php
- M Service/Album/Album.php
- M Service/Api.php
- M Service/Callback.php
- M Service/Photo.php
- M Service/Process.php


## Version 4.7.1

### Information

- **Release Date:** January 02, 2019
- **Best Compatibility:** phpFox >= 4.7.1

### Fixed Bugs

- Have to clear cache after Delete categories.
- Sponsor in Feed a photos Page/Group - Clicks Number not change after click on Sponsored Feed.
- Upload photos in Group/Page - Show wrong when click on photo from Group/Page feed.
- Mail Notification - Show wrong photo album name when a user comment on your "Profile pictures" album.
- Post multi photos from Page/Group - Show only one photo in Home feed.

### Changed Files

- M Ajax/Ajax.php
- M Controller/FrameController.php
- M Controller/FrameDragDropController.php
- M Install.php
- M README.md
- M Service/Api.php
- M Service/Callback.php
- M Service/Category/Category.php
- M Service/Category/Process.php
- M Service/Photo.php
- M Service/Process.php
- M assets/autoload.js
- M assets/main.less
- M changelog.md
- M hooks/admincp.service_maintain_delete_files_get_list.php
- M views/block/stream.html.php
- M views/controller/view.html.php


## Version 4.7.0

### Information

- **Release Date:** November 23, 2018
- **Best Compatibility:** phpFox >= 4.7.0

### Fixed Bugs

- Responsive - Edit photo: Category field issue.
- On Iphone - Photo detail - Tag friend button be floating when click 2 times.
- Profile >> Photos - Redirect Page Found when click on All Albums.
- Pages/Groups >> Photos - Redirect to all photos of Pages/Group when Click on All Albums.

### Improvements

- Add new setting to choose default mode view and support mode view on album detail.

### Changed Files

- M Ajax/Ajax.php
- M Block/Stream.php
- M Controller/AlbumController.php
- M Controller/AlbumsController.php
- M Controller/FrameController.php
- M Controller/IndexController.php
- M Controller/ViewController.php
- M Install.php
- M Installation/Database/Photo_Info.php
- M README.md
- M Service/Callback.php
- M Service/Photo.php
- M Service/Process.php
- M Service/Tag/Process.php
- M assets/autoload.js
- M assets/main.less
- M changelog.md
- M phrase.json
- M start.php
- M views/block/drop-down.html.php
- M views/block/form.html.php
- M views/block/mass-edit-item.html.php
- M views/block/menu.html.php
- M views/block/stream.html.php
- M views/controller/add.html.php
- M views/controller/album.html.php
- M views/controller/index.html.php
- M views/controller/view.html.php

### New Settings

| ID | Var name | Name | Description |
| --- | -------- | ---- | --- |
| 1 | photo_default_mode_view | Default Photo Mode View | Select a default mode to view photos |

## Version 4.6.5

### Information

- **Release Date:** October 10, 2018
- **Best Compatibility:** phpFox >= 4.7.0

### Fixed Bugs

- After finish submitting photo to pages album, URL show "//" at end. 
- Photo album posted in Secret group is visible on feed to non-member users.
- Share photo, video from user's feed: not show photo, video in shared feed.
- Wrong image size on website in photos to what it shows when download photo.
- Tooltip for view mode: Missing phrase.
- Show upload fail forever after uploaded exceed maximum photo number per upload.
- Can tag own photo after disable setting from back end.
- Manage sponsorships: Click number not change when click on a sponsored feed.
- Show duplicate on sponsored block when re-sponsor a denied item.
- Admin can not Sponsor In Feed a photo of other user.
- Sponsor album without payment: Missed album's name in manage sponsorship.
- Click number not change when click on sponsored Album in block.
- Redirect wrong page when click an item in Manage sponsorships.

### Improvements

- Add new setting to disallow/allow app to post on Main feed when add new item (album / photos). (default is allow).
- Responsive - Edit photo - Mature content - Radio buttons should show align.
- Allow to Feature and sponsor Photo Albums.

### Changed Files

- M Ajax/Ajax.php
- A Block/FeaturedAlbumBlock.php
- A Block/SponsoredAlbumBlock.php
- M Controller/AlbumController.php
- M Controller/FrameDragDropController.php
- M Controller/ViewController.php
- M Install.php
- M Installation/Database/Photo_Album.php
- M Installation/Database/Photo_Album_Info.php
- M Installation/Database/Photo_Category_Data.php
- M Installation/Database/Photo_Feed.php
- M Installation/Database/Photo_Info.php
- A Installation/Version/v465.php
- M README.md
- M Service/Album/Album.php
- M Service/Album/Process.php
- M Service/Api.php
- M Service/Browse.php
- M Service/Callback.php
- M Service/Category/Process.php
- M Service/Photo.php
- M Service/Process.php
- M assets/autoload.js
- M assets/main.less
- M changelog.md
- M installer.php
- M phrase.json
- M start.php
- M views/block/album_entry.html.php
- A views/block/featured-album.html.php
- M views/block/form.html.php
- M views/block/menu-album.html.php
- M views/block/menu.html.php
- A views/block/sponsored-album.html.php
- M views/controller/index.html.php

### Removed Settings

| ID | Var name | Name | Reason
| --- | -------- | ---- | ---
| 1 | enabled_watermark_on_photos | Watermark Photos | Don't use anymore


## Version 4.6.4

### Information

- **Release Date:** Aug 21, 2018
- **Best Compatibility:** phpFox >= 4.6.0

### Fixed Bugs
- Can not post photo when disable feed module
- Update Photos - Displaying modes view
- Should hide feature " Set as profile and cover" if user doesn't have permission to download photo
- Owner of Apps received Email with wrong language when anyone commented on it
- Not show Un-sponsor In Feed option after Sponsored In feed a photo
- Can you realign the top of page icon in photo swatch
- Guest user can see photo albums while set No for "Can view photo albums?
- Group's photo detail - Should hide "Set As Group's Cover Photo" when user does not have permission
- Can not view next photo in album when click on image from feed

### Improvements
- Show app menu when view photo detail

### Changed Files
- M Ajax/Ajax.php
- M Block/Sponsored.php
- M Controller/Admin/CategoryController.php
- M Controller/AlbumController.php
- M Controller/AlbumsController.php
- M Controller/IndexController.php
- M Controller/ViewController.php
- M Install.php
- M README.md
- M Service/Album/Process.php
- M Service/Callback.php
- M Service/Category/Category.php
- M Service/Photo.php
- M Service/Process.php
- M Service/Tag/Process.php
- M assets/autoload.js
- M assets/main.less
- M changelog.md
- M checksum
- M hooks/bundle__start.php
- M views/block/menu.html.php
- M views/controller/admincp/add.html.php
- M views/controller/index.html.php
- M views/controller/view.html.php

## Version 4.6.3

### Information

- **Release Date:** July 24, 2018
- **Best Compatibility:** phpFox >= 4.6.0

### Fixed Bugs
- "Set as Page's Cover" is visible to non-login user.
- When select a group that has not made Vanity URL tiny profile pic error to wrong page.
- Photo album swatches disappears.
- Photo's group/page - User can choose album privacy when edit album photo.
- Featured/Sponsored Photo icon does not show.
- Photo detail page - Remove "Back" button on mobile devices.

### Improvements
- Adding new mode view for listing page.

### Changed Files
- M Ajax/Ajax.php
- M Controller/FrameDragDropController.php
- M Controller/IndexController.php
- M Controller/ViewController.php
- M Install.php
- M Installation/Version/v453.php
- M Installation/Version/v460.php
- A Installation/Version/v463.php
- M README.md
- M Service/Album/Album.php
- M Service/Api.php
- M Service/Callback.php
- M Service/Photo.php
- M Service/Process.php
- M assets/autoload.js
- M assets/main.less
- M changelog.md
- M installer.php
- M views/block/featured.html.php
- M views/block/menu.html.php
- M views/block/sponsored.html.php
- M views/controller/view.html.php
- M views/controller/index.html.php

### Removed Settings

| ID | Var name | Name | Reason
| --- | -------- | ---- | ---
| 1 | photo_pic_sizes | Photo Pic Sizes | Don't use anymore

### Deprecated Settings

| ID | Var name | Name | Reason | Will Remove In |
| --- | -------- | ---- | --- | ---- |
| 1 | enabled_watermark_on_photos | Watermark Photos | Don't use anymore | 4.6.4 |

## Version 4.6.2

### Information

- **Release Date:** April 23, 2018
- **Best Compatibility:** phpFox >= 4.6.0

### Fixed Bugs

- Show duplicate feed after upload cover photo.
- Missing progress bar when uploading photos.
- Cannot get photos via api.

### Improvements
- Support hook in photo listing page.

### Changed Files
- M assets/autoload.js
- M	Controller/FrameController.php
- M	Controller/IndexController.php
- M	Service/Api.php
- M	Install.php
- M	phrase.json

## Version 4.6.1

### Information

- **Release Date:** February 13, 2018
- **Best Compatibility:** phpFox >= 4.6.0

### Fixed Bugs

- Missing SEO settings.

### Improvements
- Do not allow users make other users photo as profile photo/cover photo.

### Changed Files
- M	Controller/FrameController.php
- M	Controller/IndexController.php
- M	Install.php
- M	Installation/Version/v453.php
- M	Service/Album/Album.php
- M	Service/Callback.php
- M	assets/autoload.js
- M	assets/main.less
- M	views/controller/view.html.php

## Version 4.6.0

### Information

- **Release Date:** January 09, 2018
- **Best Compatibility:** phpFox >= 4.6.0

### Fixed Bugs

* Feed shows scale photos when sharing feed on friend's wall.
* Cannot view photo in case uploaded file has more than one extension.
* Sponsor in feed works wrong.
* Issue when load more photos many times.

### Improvements

* Support search photo albums in global search.
* Improve layout of all pages and blocks.
* Add Block Setting to limit the photos in Recent block.
* Support drag/drop, preview, progress bar when users upload photos.
* Support admin can change default photo for photo albums.
* Validate all settings, user group settings, and block settings.

### Changed Files

- Ajax/Ajax.php
- Block/Album.php
- Block/AlbumTag.php
- Block/Category.php
- Block/Detail.php
- Block/Featured.php
- Block/MyPhoto.php
- Block/Profile.php
- Block/Share.php
- Block/Sponsored.php
- Controller/AddController.php
- Controller/Admin/CategoryController.php
- Controller/AlbumController.php
- Controller/AlbumsController.php
- Controller/FrameController.php
- Controller/FrameDragDropController.php
- Controller/FrameFeedDragDropController.php
- Controller/IndexController.php
- Controller/ViewController.php
- Install.php
- Installation/Version/v453.php
- Installation/Version/v460.php
- README.md
- Service/Album/Album.php
- Service/Album/Browse.php
- Service/Album/Process.php
- Service/Api.php
- Service/Browse.php
- Service/Callback.php
- Service/Category/Category.php
- Service/Category/Process.php
- Service/Photo.php
- Service/Process.php
- Service/Tag/Process.php
- Service/Tag/Tag.php
- assets/autoload.css
- assets/autoload.js
- assets/autoload.less
- assets/dropzone/dropzone.css
- assets/dropzone/dropzone.js
- assets/images/nocover.jpg
- assets/images/nocover.png
- assets/main.less
- change-log.md
- hooks/bundle__start.php
- hooks/validator.admincp_user_settings_photo.php
- phrase.json
- start.php
- views/block/album-tag.html.php
- views/block/album_entry.html.php
- views/block/featured.html.php
- views/block/form-album.html.php
- views/block/form.html.php
- views/block/mass-edit-item.html.php
- views/block/menu-album.html.php
- views/block/menu.html.php
- views/block/my-photo.html.php
- views/block/photo_entry.html.php
- views/block/share.html.php
- views/block/sponsored.html.php
- views/block/stream.html.php
- views/controller/add.html.php
- views/controller/admincp/add.html.php
- views/controller/album.html.php
- views/controller/albums.html.php
- views/controller/edit-album.html.php
- views/controller/index.html.php
- views/controller/view.html.php


### New Settings

| ID | Var name | Name | Description |
| --- | -------- | ---- | --- |
| 1 | display_timeline_photo_within_gallery | Display User Timeline Photos within Gallery | Allow admin to decide show/hide uploaded photos from feed in Photos listing page |

### Deprecated Settings

| ID | Var name | Name | Reason | Will Remove In |
| --- | -------- | ---- | --- | ---- |
| 1 | photo_pic_sizes | Photo Pic Sizes | Don't use anymore | 4.6.3 |

## Version 4.5.3

### Information

- **Release Date:** September 22th, 2017
- **Best Compatibility:** phpFox >= 4.5.3

### Removed Settings

| ID | Var name | Name | Reason |
| --- | -------- | ---- | --- |
| 1 | photo_image_details_time_stamp | Image Details Time Stamps | Don't use anymore |
| 2 | html5_upload_photo | HTML5 Mass Upload | Don't use anymore |
| 3 | can_add_tags_on_photos | Can add tags on photos? | Don't use anymore |
| 4 | can_edit_photo_categories | Can edit public photo categories? | Don't use anymore |
| 5 | can_add_public_categories | Can add public photo categories? | Don't use anymore |
| 6 | total_photo_display_profile | Define how many photos to display within an album on a users profile. | Don't use anymore |

### New Settings

| ID | Var name | Name | Description |
| --- | -------- | ---- | ---- |
| 1 | photo_paging_mode | Pagination Style | Select Pagination Style at Search Page. |
| 2 | display_cover_photo_within_gallery | Display User Cover Photos within Gallery | Disable this feature if you do not want to display user cover photos within the photo gallery. |
| 3 | display_photo_album_created_in_group | Display photos/albums which created in Group to the Photo app | Enable to display all public photos/albums to the both Photos/Albums page in group detail and in Photo app. Disable to display photos/albums created by an users to the both Photos/Albums page in group detail and My Photos/Albums page of this user in Photo app and nobody can see these photos/albums in Photo app but owner. Notice: This setting will be applied for all types of groups, include secret groups. |
| 3 | display_photo_album_created_in_page | Display photos/albums which created in Page to the Photo app | Enable to display all public photos/albums to the both Photos/Albums page in page detail and in Photo app. Disable to display photos/albums created by an users to the both Photos/Albums page in page detail and My Photos/Albums page of this user in Photo app and nobody can see these photos/albums in Photo app but owner. |
| 4 | can_post_on_albums | Can post comments on albums? | Can post comments on albums? |

### Deprecated Functions

| ID | Class Name | Function Name | Will Remove In | Reason |
| --- | -------- | ---- | ---- | ---- |
| 1 | Apps\Core_Photos\Ajax\Ajax | browse | 4.6.0 | Don't use anymore |
| 2 | Apps\Core_Photos\Ajax\Ajax | browseUserAlbum | 4.6.0 | Don't use anymore |
| 3 | Apps\Core_Photos\Ajax\Ajax | browseAlbum | 4.6.0 | Don't use anymore |
| 4 | Apps\Core_Photos\Ajax\Ajax | browseUserPhotos | 4.6.0 | Don't use anymore |
| 5 | Apps\Core_Photos\Ajax\Ajax | categoryOrdering | 4.6.0 | Don't use anymore |
| 6 | Apps\Core_Photos\Service\Callback | deleteGroup | 4.6.0 | Don't use anymore |
| 7 | Apps\Core_Photos\Service\Photo | _getPhoto | 4.6.0 | Don't use anymore |
| 8 | Apps\Core_Photos\Service\Photo | getPreviousPhotos | 4.6.0 | Don't use anymore |
| 9 | Apps\Core_Photos\Service\Photo | getNextPhotos | 4.6.0 | Don't use anymore |
| 10 | Apps\Core_Photos\Service\Photo | getPhotoStream | 4.6.0 | Don't use anymore |
| 11 | Apps\Core_Photos\Service\Photo | getInfoForAction | 4.6.0 | Don't use anymore |




