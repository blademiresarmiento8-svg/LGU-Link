<?php
/**
 * Switches for parts of the system that exist but are not shown for now.
 *
 * NEWS_EMAILS_VISIBLE: the screens for emailing news and announcements to citizens (the "Get news by email" card
 * on the News page, and the system admin's News Emails tab and page). While it is false they cannot be seen or
 * reached. The sender itself is untouched: anyone who already subscribed, and any email already waiting, is still
 * handled in the background. Set it to true to show the screens again.
 */
const NEWS_EMAILS_VISIBLE = false;
