# Forum :: Change Log

## Version 4.6.3

### Information

- **Release Date:** December 21, 2018
- **Best Compatibility:** phpFox >= 4.6.1

### Improvements

- Support utf8_mb4.
- Show actial statistics on detail page.
- Add limit when get annoucements.

### Fix bugs

- Show error when add reply.
- Cross site scripting.

### Changed files

- M Controller/ForumController.php
- M Install.php
- A Installation/Version/v463.php
- M Service/Post/Post.php
- M Service/Thread/Thread.php
- M assets/main.less
- M installer.php
- M views/block/forum.html.php
- M views/block/thread-entry.html.php
- M views/block/thread-rows.html.php

## Version 4.6.2

### Information

- **Release Date:** October 28, 2018
- **Best Compatibility:** phpFox >= 4.6.0

### Improvements

- Improve performance.
- Add new setting to disallow/allow app to post on Main feed when add new item. (default is allow).

### Fix bugs

- Forum tags (trending) don't get removed after deleting thread.
- Response ajax link wrong when un-sponsor.
- Forum sponsored not working right because in edit mode sponsor thread not working with ads also it doubles sponsored in advertise.
- Show repeat a thread whenever add post to that thread.
- Show sponsor action in drop-down menu when user has no permission.
- Post reply to thread: Show wrong notice message.
- Show duplicate on sponsored block when re-sponsor a denied item.
- Show sub-menu of forum when click on discussion tab.

### Changed files

- M Ajax/Ajax.php
- M Block/RecentPostBlock.php
- M Block/RecentThreadBlock.php
- M Controller/ForumController.php
- M Controller/IndexController.php
- M Controller/RecentController.php
- M Controller/ThreadController.php
- M Install.php
- M Installation/Database/Forum_Moderator_Access.php 
- M Installation/Database/Forum_Post_Text.php
- M README.md
- M Service/Callback.php
- M Service/Forum.php
- M Service/Moderate/Process.php
- M Service/Post/Post.php
- M Service/Post/Process.php
- M Service/Process.php
- M Service/Thread/Process.php
- M Service/Thread/Thread.php
- M assets/autoload.js
- M change-log.md
- M phrase.json
- M views/block/menu.html.php
- M views/block/search.html.php
- M views/controller/forum.html.php

## Version 4.6.1

### Information

- **Release Date:** Aug 21, 2018
- **Best Compatibility:** phpFox >= 4.6.0

### Fix bugs

- SEO - No phrase input for description and keywords
- User can not see their post when replies the thread from "recent posts" block
- Broken layout when disable feed module
- Display an option not permission in menu
- Should show app menu on all pages
- Created feed is still displaying banned word
- Not display inserted photo when post reply
- Wrong result for searching in My Threads page
- In forum app in admincp there is a typo "detele" instead of "delete"
- Still display banned words in Recent Discussions, Recent Posts blocks
- Inserted photo be crossed

### Changed files
- M Ajax/Ajax.php
- M Block/ReplyBlock.php
- M Block/SponsoredBlock.php
- M Controller/Admin/AddController.php
- M Controller/Admin/DeleteController.php
- M Controller/ForumController.php
- M Controller/PostController.php
- M Controller/ThreadController.php
- M Install.php
- M README.md
- M Service/Callback.php
- M Service/Forum.php
- M Service/Process.php
- M Service/Thread/Process.php
- M Service/Thread/Thread.php
- M assets/autoload.js
- D assets/autoload.less
- M assets/main.less
- M change-log.md
- M phrase.json
- M views/block/entry.html.php
- M views/block/forum.html.php
- M views/block/post.html.php
- M views/block/recent-post.html.php
- M views/block/reply.html.php
- M views/block/search.html.php
- M views/block/sponsored.html.php
- M views/block/thread-detail.html.php
- M views/block/thread-entry.html.php
- M views/controller/admincp/index.html.php
- M views/controller/thread.html.php

## Version 4.6.0

### Information

- **Release Date:** January 09, 2018
- **Best Compatibility:** phpFox >= 4.6.0

### Improvements

- Allow admin can view and approve/deny pending posts in thread detail page.
- Support both of topic and hashtag.
- Hide `Reply` button and thread tools in closed threads detail pages.
- Users can select actions of thread/post on listing page same as on thread detail page.
- Support AddThis in thread detail page.
- Support 3 styles for paginations.
- Validate all settings, user group settings, and block settings.

### Removed Settings

| ID | Var name | Name | Reason |
| --- | -------- | ---- | --- |
| 1 | total_recent_posts_display | Total Recent Posts Display | Don't use anymore |
| 2 | total_recent_discussions_display | Total Recent Discussions Display | Don't use anymore |
| 3 | forum_user_time_stamp | Forum User Time Stamp | Don't use anymore |
| 4 | can_add_tags_on_threads | Can add tags to threads? | Don't use anymore |

### New Settings

| ID | Var name | Name | Description |
| --- | -------- | ---- | ---- |
| 1 | forum_paging_mode | Pagination Style | Select Pagination Style at Search Page. |
| 2 | forum_meta_description | Forum Meta Description | Meta description added to pages related to the Forum app. |
| 3 | forum_meta_keywords | Forum Meta Keywords | Meta keywords that will be displayed on sections related to the Forum app. |
| 4 | default_search_type | Default option to search in main forum page | |

### Removed Blocks

| ID | Block | Name |  Reason |
| --- | -------- | ---- | ---- |
| 1 | forum.recent | Recent Threads | Don't use anymore |

### New Blocks

| ID | Block | Name | Description |
| --- | -------- | ---- | ------------ |
| 1 | forum.recent-post | Recent Posts | Show recent posts of forum |
| 2 | forum.recent-thread | Recent Discussions | Show recent threads of forum |
| 2 | forum.sponsored | Sponsored Threads | Show sponsored threads of forum |


