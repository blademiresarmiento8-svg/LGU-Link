<?php
/**
 * Single reusable "Are you sure?" confirmation modal, shared site-wide.
 * Reuses the standard .modal-overlay / .modal-box styles from style.css.
 * Trigger it from JS with confirmAction({ title, message, confirmLabel, danger, onConfirm }).
 */
?>
  <div class="modal-overlay" id="confirmModal">
    <div class="modal-box confirm-modal-box">
      <div class="modal-header">
        <h3 id="confirmModalTitle">Please Confirm</h3>
        <i class="fa-solid fa-xmark close-btn" onclick="closeModal('confirmModal')"></i>
      </div>
      <p id="confirmModalMessage" class="confirm-modal-message">Are you sure you want to proceed?</p>
      <div class="modal-actions">
        <button type="button" class="btn-modal btn-cancel" onclick="closeModal('confirmModal')">Cancel</button>
        <button type="button" class="btn-modal btn-submit" id="confirmModalConfirmBtn">Confirm</button>
      </div>
    </div>
  </div>
