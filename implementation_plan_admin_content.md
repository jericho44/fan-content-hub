# Admin Content Page Adjustment Plan

This plan outlines improvements to the admin content management interface to provide a better user experience and fix missing functionality.

## Proposed Changes

### [Backend] API

#### [MODIFY] [ContentController.php](file:///c:/laragon/www/fan-content-hub-platform/app/Http/Controllers/ApiWeb/ContentController.php)
- Update [update](file:///c:/laragon/www/fan-content-hub-platform/app/Services/ContentService.php#40-44) method to allow changing `event_id`.
- Ensure tags are properly synced during update.

### [Frontend] Admin UI

#### [MODIFY] [Index.vue](file:///c:/laragon/www/fan-content-hub-platform/resources/js/views/admin/contents/Index.vue)
- **Visual Thumbnails**: Replace the "Lihat" button with a small, clickable thumbnail preview in the data table.
- **Form Enhancements**:
  - Add a **Tags** multi-select field to the modal (fix missing functionality).
  - Add **Event** selector to the **Edit** mode (currently only available on Insert).
- **Status UI**: Improve the status badges for better visibility.

## Verification Plan

### Manual Verification
- Log in to the admin panel -> Konten.
- Verify that thumbnails are displayed correctly in the list.
- Edit a content item and verify that:
  - You can change its Event.
  - You can add/remove Tags.
  - The status change works as expected.
- Verify that the tags are saved and displayed correctly in the public gallery.
