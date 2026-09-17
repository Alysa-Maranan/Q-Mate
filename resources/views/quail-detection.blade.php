@extends('layouts.app')

@section('content')
@include('components.sidebar')

<style>
    :root {
        --color-primary: #6D4C41;
        --color-primary-dark: #4E342E;
        --color-accent: #A1887F;
        --bg-page: #F5F0EB;
        --surface: #FFFFFF;
        --muted: #8D6E63;
        --border: #E0D6D2;
        --success: #27AE60;
        --warning: #FF9800;
        --danger: #F44336;
        --radius-card: 16px;
        --radius-btn: 10px;
        --shadow-card: 0 6px 20px rgba(109,76,65,0.08);
        --shadow-modal: 0 10px 40px rgba(0,0,0,0.2);
        --transition-fast: 150ms;
    }

    body {
        background: linear-gradient(135deg, #f5f0eb 0%, #efe8e4 50%, #e8dcd6 100%);
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    }

    .sidebar-content-wrap {
        margin-left: 300px;
        transition: margin-left 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        padding-top: 0;
    }

    .sidebar-content-wrap.expanded {
        margin-left: 0;
        padding-top: 80px;
    }

    .sidebar-content-wrap.expanded .detection-container {
        max-width: 1600px;
        margin: 0 auto;
        padding-left: 2rem;
        padding-right: 2rem;
    }

    .detection-container {
        max-width: 100%;
        margin: 0 auto;
        padding: 2rem;
    }

    .detection-header {
        background: white;
        border-radius: 24px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 8px 32px rgba(161, 136, 127, 0.12);
        border: 1px solid rgba(161, 136, 127, 0.1);
        text-align: center;
    }

    .detection-header h1 {
        font-size: 2rem;
        font-weight: 800;
        color: #6d4c41;
        margin: 0;
    }

    .detection-header p {
        margin: 1rem 0 0 0;
        color: #8d6e63;
        font-size: 1rem;
    }

    .camera-section {
        background: white;
        border-radius: 20px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 8px 32px rgba(161, 136, 127, 0.12);
        border: 1px solid rgba(161, 136, 127, 0.1);
    }

    .camera-container {
        position: relative;
        width: 100%;
        max-width: 720px;
        margin: 0 auto;
        border-radius: var(--radius-card);
        overflow: hidden;
        background: #000;
        aspect-ratio: 4/3;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: var(--shadow-card);
    }

    #overlay-canvas {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
        z-index: 5;
    }

    #canvas-preview {
        display: none !important;
    }

    #video-feed {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    #canvas-preview {
        display: none;
        width: 100%;
        height: 100%;
    }

    .camera-container {
        position: relative;
        overflow: hidden;
    }

    #overlay-canvas {
        position: absolute;
        left: 0;
        top: 0;
        pointer-events: none;
        z-index: 5;
        display: block;
    }

    .camera-controls, .control-bar {
        display: flex;
        gap: 0.75rem;
        justify-content: center;
        align-items: center;
        margin-top: 1rem;
        flex-wrap: wrap;
    }

    .control-bar {
        padding: 0.5rem;
    }

    .control-bar .btn {
        min-height: 44px;
        padding: 0.6rem 1rem;
    }

    .btn {
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: var(--radius-btn);
        font-weight: 600;
        cursor: pointer;
        transition: transform var(--transition-fast) ease, box-shadow var(--transition-fast) ease;
        font-size: 0.95rem;
        min-height: 44px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    .btn-primary {
        background: linear-gradient(135deg, #6d4c41 0%, #a1887f 100%);
        color: white;
        border: 2px solid transparent;
    }

    .btn-primary:hover {
        box-shadow: 0 6px 20px rgba(109, 76, 65, 0.3);
        transform: translateY(-2px);
    }

    .btn-secondary {
        background: #efebe9;
        color: #6d4c41;
        border: 2px solid #a1887f;
    }

    .btn-secondary:hover {
        background: #f5eee6;
    }

    .btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .detection-result {
        margin-top: 2rem;
        padding: 1.5rem;
        border-radius: 16px;
        border: 2px solid #d7ccc8;
        min-height: 100px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        text-align: center;
        display: none;
    }

    .detection-result.show {
        display: flex;
    }

    .detection-result.success {
        background: #e8f5e9;
        border-color: #4caf50;
    }

    .detection-result.warning {
        background: #fff3e0;
        border-color: #ff9800;
    }

    .detection-result.error {
        background: #ffebee;
        border-color: #f44336;
    }

    .detection-status-icon {
        font-size: 2.5rem;
        margin-bottom: 0.5rem;
    }

    .detection-status-text {
        font-size: 1.25rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .detection-result.success .detection-status-text {
        color: #2e7d32;
    }

    .detection-result.warning .detection-status-text {
        color: #e65100;
    }

    .detection-result.error .detection-status-text {
        color: #c62828;
    }

    .detection-details {
        font-size: 0.95rem;
        color: #666;
        margin-top: 0.5rem;
    }

    .breed-info {
        background: #efebe9;
        border-radius: 12px;
        padding: 1.5rem;
        margin-top: 1.5rem;
        border-left: 4px solid #a1887f;
        display: none;
    }

    .breed-info.show {
        display: block;
    }

    .breed-info h3 {
        margin: 0 0 1rem 0;
        color: #6d4c41;
        font-size: 1.1rem;
    }

    .breed-info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 1rem;
    }

    .breed-info-item {
        background: white;
        padding: 1rem;
        border-radius: 8px;
        border: 1px solid #d7ccc8;
    }

    .breed-info-label {
        font-size: 0.85rem;
        font-weight: 600;
        color: #8d6e63;
        text-transform: uppercase;
        margin-bottom: 0.5rem;
    }

    .breed-info-value {
        font-size: 1rem;
        font-weight: 700;
        color: #6d4c41;
    }

    .confidence-bar {
        width: 100%;
        height: 8px;
        background: #e0e0e0;
        border-radius: 4px;
        margin-top: 0.5rem;
        overflow: hidden;
    }

    .confidence-fill {
        height: 100%;
        background: linear-gradient(90deg, #66bb6a 0%, #43a047 100%);
        width: 0%;
        transition: width 0.3s ease;
    }

    .camera-status {
        font-size: 0.9rem;
        color: var(--muted);
        margin-top: 1rem;
        text-align: center;
    }

    .status-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: var(--surface);
        border: 1px solid var(--border);
        color: var(--color-primary);
        padding: 0.4rem 0.75rem;
        border-radius: 999px;
        font-weight: 600;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        font-size: 0.9rem;
    }

    .status-chip .status-icon {
        font-size: 1rem;
    }

    .icon {
        display: inline-block;
        vertical-align: middle;
        fill: currentColor;
        stroke: currentColor;
    }

    .btn-icon {
        width: 18px;
        height: 18px;
    }

    .icon[width] {
        width: auto;
    }

    .status-chip.loading {
        background: linear-gradient(90deg, rgba(161,136,127,0.05), rgba(161,136,127,0.02));
    }

    .detection-model-info {
        background: #efebe9;
        border-radius: 12px;
        padding: 1rem;
        margin-top: 2rem;
        text-align: center;
        color: #8d6e63;
        font-size: 0.9rem;
    }

    .history-section {
        background: white;
        border-radius: 20px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 8px 32px rgba(161, 136, 127, 0.12);
        border: 1px solid rgba(161, 136, 127, 0.1);
    }

    .history-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }

    .history-title {
        color: #6d4c41;
        font-size: 1.2rem;
        font-weight: 700;
        margin: 0;
    }

    .history-filters {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .filter-btn {
        padding: 0.5rem 1rem;
        border: 2px solid #d7ccc8;
        border-radius: 8px;
        background: white;
        color: #6d4c41;
        font-weight: 600;
        font-size: 0.85rem;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .filter-btn:hover {
        border-color: #a1887f;
        background: #f5f5f5;
    }

    .filter-btn.active {
        background: #a1887f;
        color: white;
        border-color: #a1887f;
    }

    .delete-all-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 0.5rem 1rem;
        margin-left: 0.5rem;
        border: 2px solid #f44336;
        border-radius: 8px;
        background: white;
        color: #f44336;
        font-weight: 600;
        font-size: 0.85rem;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .delete-all-btn:hover:not(:disabled) {
        background: #f44336;
        color: white;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(244, 67, 54, 0.3);
    }

    .delete-all-btn:disabled {
        opacity: 0.45;
        cursor: not-allowed;
    }

    .history-list {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .history-item {
        display: flex;
        gap: 1rem;
        padding: 1rem;
        border-radius: 12px;
        border: 2px solid #efebe9;
        background: #fafafa;
        transition: all 0.3s ease;
        position: relative;
    }

    .history-item:hover {
        border-color: #d7ccc8;
        background: white;
        box-shadow: 0 4px 12px rgba(161, 136, 127, 0.1);
    }

    .history-delete-btn {
        position: absolute;
        top: 0.5rem;
        right: 0.5rem;
        width: 32px;
        height: 32px;
        border-radius: 8px;
        border: 2px solid #f44336;
        background: white;
        color: #f44336;
        font-size: 1rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        z-index: 10;
    }

    .history-delete-btn:hover {
        background: #f44336;
        color: white;
        transform: scale(1.1);
    }

    .history-image {
        width: 80px;
        height: 80px;
        border-radius: 8px;
        object-fit: cover;
        flex-shrink: 0;
        background: #e0e0e0;
    }

    .history-content {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .history-breed {
        font-size: 1rem;
        font-weight: 700;
        color: #6d4c41;
    }

    .history-type {
        font-size: 0.85rem;
        color: #8d6e63;
    }

    .history-confidence {
        font-size: 0.85rem;
        color: #666;
    }

    .history-date {
        font-size: 0.8rem;
        color: #999;
        margin-top: 0.25rem;
    }

    .history-empty {
        text-align: center;
        padding: 3rem 1rem;
        color: #a1887f;
    }

    .history-empty-icon {
        font-size: 3rem;
        margin-bottom: 1rem;
    }

    .history-empty-text {
        font-size: 1rem;
        font-weight: 600;
    }

    /* Modal Styles */
    .modal {
        display: none;
        position: fixed;
        z-index: 99999;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.6);
        animation: fadeIn 0.3s ease;
        pointer-events: none;
    }

    .modal.show {
        display: flex;
        align-items: center;
        justify-content: center;
        pointer-events: auto;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    .modal-content {
        position: relative;
        z-index: 100000;
        background: white;
        border-radius: 20px;
        padding: 1.5rem;
        max-width: 900px;
        width: 95%;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
        animation: slideUp 0.3s ease;
        display: block;
        pointer-events: auto;
    }

    .modal-body {
        display: grid;
        grid-template-columns: 1fr 1.1fr;
        gap: 1rem;
        align-items: start;
    }

    .modal-left {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .modal-thumb {
        width: 100%;
        max-width: 320px;
        border-radius: 12px;
        overflow: hidden;
        background: #f5f5f5;
        border: 1px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 180px;
    }

    .modal-thumb img {
        width: 100%;
        height: auto;
        display: block;
        object-fit: cover;
    }

    .modal-right {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    @media (max-width: 768px) {
        .modal-body {
            grid-template-columns: 1fr;
        }

        .modal-content {
            padding: 1rem;
        }

        .modal-thumb {
            max-width: 100%;
            min-height: 160px;
        }
    }

    @keyframes slideUp {
        from {
            transform: translateY(30px);
            opacity: 0;
        }

        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        border-bottom: 2px solid #f5eee6;
        padding-bottom: 1rem;
        position: relative;
        z-index: 100001;
    }

    .modal-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #6d4c41;
        margin: 0;
    }

    .modal-close {
        background: none;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
        color: #a1887f;
        padding: 0;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        z-index: 100002;
        pointer-events: auto !important;
        touch-action: manipulation;
    }

    .modal-close:hover {
        color: #6d4c41;
    }

    .modal-close:focus {
        outline: 2px solid #a1887f;
        outline-offset: 2px;
    }

    .modal-result {
        margin-bottom: 2rem;
    }

    .modal-status-text {
        font-size: 1.3rem;
        font-weight: 700;
        color: #6d4c41;
        margin-bottom: 0.5rem;
    }

    .modal-status-details {
        font-size: 1rem;
        color: #8d6e63;
        margin-bottom: 1rem;
    }

    .modal-confidence {
        font-size: 0.95rem;
        color: #666;
        margin-bottom: 0.5rem;
    }

    .modal-confidence-bar {
        width: 100%;
        height: 10px;
        background: #e0e0e0;
        border-radius: 5px;
        overflow: hidden;
    }

    .modal-confidence-fill {
        height: 100%;
        background: linear-gradient(90deg, #66bb6a 0%, #43a047 100%);
        width: 0%;
        transition: width 0.3s ease;
    }

    .modal-breed-info {
        background: #efebe9;
        border-radius: 16px;
        padding: 1.5rem;
        text-align: left;
        border-left: 4px solid #a1887f;
    }

    .modal-breed-title {
        font-size: 1.2rem;
        font-weight: 700;
        color: #6d4c41;
        margin: 0 0 1rem 0;
    }

    .modal-breed-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }

    .modal-breed-item {
        background: white;
        padding: 0.75rem;
        border-radius: 8px;
        border: 1px solid #d7ccc8;
    }

    .modal-breed-label {
        font-size: 0.75rem;
        font-weight: 600;
        color: #8d6e63;
        text-transform: uppercase;
        margin-bottom: 0.25rem;
    }

    .modal-breed-value {
        font-size: 0.9rem;
        font-weight: 700;
        color: #6d4c41;
    }

    .modal-breed-description {
        margin-top: 1rem;
        color: #666;
        font-size: 0.95rem;
        line-height: 1.5;
    }

    .modal-buttons {
        display: flex;
        gap: 1rem;
        margin-top: 1.5rem;
        justify-content: center;
        position: relative;
        z-index: 100001;
    }

    .modal-btn {
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 10px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 0.95rem;
        position: relative;
        z-index: 100002;
        pointer-events: auto !important;
        touch-action: manipulation;
    }

    .modal-btn-primary {
        background: linear-gradient(135deg, #6d4c41 0%, #a1887f 100%);
        color: white;
    }

    .modal-btn-primary:hover {
        box-shadow: 0 6px 20px rgba(109, 76, 65, 0.3);
        transform: translateY(-2px);
    }

    .modal-btn-secondary {
        background: #efebe9;
        color: #6d4c41;
        border: 2px solid #a1887f;
    }

    .modal-btn-secondary:hover {
        background: #e5dedb;
    }

    .icon-btn {
        width: 40px;
        height: 40px;
        padding: 0.35rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
    }

    .delete-modal {
        display: none;
        position: fixed;
        z-index: 1100;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.6);
        animation: fadeIn 0.3s ease;
    }

    .delete-modal.show {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .delete-modal-content {
        background: white;
        border-radius: 20px;
        padding: 2rem;
        max-width: 450px;
        width: 90%;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
        animation: slideUp 0.3s ease;
    }

    .delete-modal-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #6d4c41;
        margin: 0 0 0.5rem 0;
    }

    .delete-modal-message {
        font-size: 1rem;
        color: #8d6e63;
        line-height: 1.5;
    }

    .delete-modal-buttons {
        display: flex;
        gap: 1rem;
        margin-top: 1.5rem;
        justify-content: center;
    }

    .delete-modal-btn {
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 10px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 0.95rem;
        min-width: 120px;
    }

    .delete-modal-btn.cancel {
        background: #efebe9;
        color: #6d4c41;
        border: 2px solid #a1887f;
    }

    .delete-modal-btn.cancel:hover {
        background: #f5eee6;
    }

    .delete-modal-btn.confirm {
        background: linear-gradient(135deg, #6d4c41 0%, #a1887f 100%);
        color: white;
    }

    .delete-modal-btn.confirm:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(109, 76, 65, 0.3);
    }

    @media (max-width: 768px) {
        .detection-container {
            padding: 1rem;
        }

        .camera-section {
            padding: 1rem;
        }

        .detection-header h1 {
            font-size: 1.5rem;
        }

        .camera-controls {
            flex-direction: column;
        }

        .btn {
            width: 100%;
        }

        .modal-content {
            padding: 1.5rem;
            max-width: 95%;
        }

        .modal-breed-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<!-- SVG sprite definitions -->
<svg xmlns="http://www.w3.org/2000/svg" style="display:none">
  <symbol id="icon-camera" viewBox="0 0 24 24">
    <rect x="3" y="7" width="18" height="13" rx="2" fill="currentColor"></rect>
    <circle cx="12" cy="13" r="3.5" fill="#fff"></circle>
    <rect x="1.5" y="4" width="21" height="3" rx="1" fill="currentColor"></rect>
  </symbol>

  <symbol id="icon-play" viewBox="0 0 24 24">
    <path d="M8 5v14l11-7z" fill="currentColor"></path>
  </symbol>

  <symbol id="icon-stop" viewBox="0 0 24 24">
    <rect x="6" y="6" width="12" height="12" rx="2" fill="currentColor"></rect>
  </symbol>

  <symbol id="icon-check" viewBox="0 0 24 24">
    <path d="M20 6L9 17l-5-5" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"></path>
  </symbol>

  <symbol id="icon-cross" viewBox="0 0 24 24">
    <path d="M6 6l12 12M18 6L6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
  </symbol>

  <symbol id="icon-question" viewBox="0 0 24 24">
    <path d="M12 18h.01M10.07 7.07a3 3 0 1 1 3.86 3.86c-.7.7-1.07 1.43-1.07 2.07" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"></path>
  </symbol>

  <symbol id="icon-search" viewBox="0 0 24 24">
    <path d="M21 21l-4.35-4.35" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"></path>
    <circle cx="11" cy="11" r="6" stroke="currentColor" stroke-width="2" fill="none"></circle>
  </symbol>

  <symbol id="icon-book" viewBox="0 0 24 24">
    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"></path>
    <path d="M4 4.5A2.5 2.5 0 0 1 6.5 7H20v12H6.5A2.5 2.5 0 0 0 4 21V4.5z" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"></path>
  </symbol>

  <symbol id="icon-person" viewBox="0 0 24 24">
    <circle cx="12" cy="8" r="3" stroke="currentColor" stroke-width="2" fill="none"></circle>
    <path d="M4 20s1-4 8-4 8 4 8 4" stroke="currentColor" stroke-width="2" fill="none"></path>
  </symbol>

  <symbol id="icon-quail" viewBox="0 0 64 64">
    <rect width="64" height="64" rx="8" fill="#e6fff3"/>
    <g transform="translate(6,10)">
      <ellipse cx="22" cy="20" rx="14" ry="11" fill="#10b981" />
      <circle cx="30" cy="14" r="3" fill="#fff" />
      <path d="M8 22 q6 -8 12 0" fill="#059669" />
      <polygon points="34,24 40,18 34,20" fill="#f59e0b" />
    </g>
  </symbol>

  <symbol id="icon-robot" viewBox="0 0 24 24">
    <rect x="4" y="3" width="16" height="12" rx="2" stroke="currentColor" stroke-width="2" fill="none"></rect>
    <rect x="7" y="7" width="2" height="2" fill="currentColor"></rect>
    <rect x="15" y="7" width="2" height="2" fill="currentColor"></rect>
    <rect x="9" y="12" width="6" height="2" rx="1" fill="currentColor"></rect>
    <path d="M12 3v-1" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
  </symbol>

  <symbol id="icon-eye" viewBox="0 0 24 24">
    <path d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7S2 12 2 12z" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"></path>
    <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2" fill="none"></circle>
  </symbol>

  <symbol id="icon-eye-off" viewBox="0 0 24 24">
    <path d="M3 3l18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"></path>
    <path d="M2 12s4-7 10-7c2.08 0 3.98.5 5.61 1.35M14.12 14.12A3 3 0 0 1 9.88 9.88" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"></path>
  </symbol>
</svg>

<div id="main-content-wrap" class="sidebar-content-wrap">
    <div class="detection-container">

        <div class="detection-header">
            <h1>Quail Detection</h1>
            <p>Point your camera at a quail to identify its type and get detailed information</p>
        </div>

        <div class="camera-section">
            <h2 style="color: #6d4c41; margin: 0 0 1.5rem 0; font-size: 1.2rem; font-weight: 700;">Live Camera Feed</h2>

            <div class="camera-container">
                <video id="video-feed" autoplay playsinline style="display: none;"></video>
                <canvas id="canvas-preview" style="display:none;"></canvas>
                <canvas id="overlay-canvas" aria-hidden="true"></canvas>

                <div id="camera-placeholder" style="color: #ccc; text-align: center; display: flex; flex-direction: column; align-items: center; justify-content: center;">
                    <svg class="icon icon-camera" width="48" height="48" aria-hidden="true">
                        <use xlink:href="#icon-camera"/>
                    </svg>
                    <div>Camera is off — click "Start Camera" to begin</div>
                </div>
            </div>

            <div class="camera-status">
                <div id="camera-status" class="status-chip" role="status" aria-live="polite">
                    <span class="status-icon">
                        <svg class="icon" width="16" height="16" aria-hidden="true">
                            <use xlink:href="#icon-question"/>
                        </svg>
                    </span>
                    <span class="status-text">Camera stopped — click Start Camera</span>
                </div>
            </div>

            <div class="camera-controls">
                <button class="btn btn-primary" id="start-btn" onclick="startCamera()" aria-label="Start camera" type="button">
                    <svg class="btn-icon" width="18" height="18" aria-hidden="true">
                        <use xlink:href="#icon-play"/>
                    </svg>
                    Start Camera
                </button>

                <button class="btn btn-secondary" id="stop-btn" onclick="stopCamera()" style="display: none;" aria-label="Stop camera" type="button">
                    <svg class="btn-icon" width="18" height="18" aria-hidden="true">
                        <use xlink:href="#icon-stop"/>
                    </svg>
                    Stop Camera
                </button>
            </div>

            <div class="detection-model-info">
                <svg class="icon icon-robot" width="18" height="18" aria-hidden="true">
                    <use xlink:href="#icon-robot"/>
                </svg>
                Using TensorFlow.js with COCO-SSD model for real-time object detection
            </div>

            <div class="tutorial-section" style="margin-top: 1.5rem; padding: 1rem; background: linear-gradient(135deg, #fff8e1 0%, #fff3e0 100%); border-radius: 12px; border: 1px solid #ffe0b2;">
                <h3 style="color: #e65100; margin: 0 0 0.75rem 0; font-size: 1rem; font-weight: 700;">
                    <svg class="icon icon-book" width="18" height="18" aria-hidden="true" style="vertical-align: middle; margin-right: 6px;">
                        <use xlink:href="#icon-book"/>
                    </svg>
                    How to Use
                </h3>

                <ol style="margin: 0; padding-left: 1.25rem; color: #5d4037; font-size: 0.875rem; line-height: 1.6;">
                    <li><strong>Click "Start Camera"</strong> to enable your webcam</li>
                    <li><strong>Point the camera</strong> at your quails in good lighting</li>
                    <li><strong>Hold steady</strong> - the system auto-detects every 2 seconds</li>
                    <li><strong>Quail detected?</strong> Breed info will appear automatically</li>
                    <li><strong>Human detected?</strong> You'll see an alert (for security)</li>
                </ol>
            </div>
        </div>

        <div class="history-section">
            <div class="history-header">
                <h2 class="history-title">Detection History</h2>

                <div class="history-filters">
                    <button class="filter-btn active" data-filter="all" onclick="filterHistory('all', event)">All</button>
                    <button class="filter-btn" data-filter="quail" onclick="filterHistory('quail', event)">Quail</button>
                    <button class="filter-btn" data-filter="human" onclick="filterHistory('human', event)">Human</button>

                    <button class="delete-all-btn" id="delete-all-btn" onclick="clearAllHistory()" title="Delete all detection records">
                        <svg class="icon" width="14" height="14" aria-hidden="true">
                            <use xlink:href="#icon-cross"/>
                        </svg>
                        Delete All
                    </button>
                </div>
            </div>

            <div class="history-list" id="history-list">
                <div class="history-empty">
                    <div class="history-empty-icon">
                        <svg class="icon icon-search" width="36" height="36" aria-hidden="true">
                            <use xlink:href="#icon-search"/>
                        </svg>
                    </div>
                    <div class="history-empty-text">No detections yet. Start the camera to detect quails!</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Detection Result Modal -->
<div id="detectionModal" class="modal" aria-hidden="true">
    <div class="modal-content" role="dialog" aria-modal="true" aria-labelledby="detection-modal-title">

        <div class="modal-header">
            <h2 class="modal-title" id="detection-modal-title">Detection Result</h2>

            <button
                type="button"
                class="modal-close"
                id="detectionModalCloseButton"
                aria-label="Close result dialog"
                title="Close"
            >
                <svg class="icon" width="18" height="18" aria-hidden="true">
                    <use xlink:href="#icon-cross"/>
                </svg>
            </button>
        </div>

        <div class="modal-result">
            <div class="modal-body" style="display: grid; grid-template-columns: 1fr 1.2fr; gap: 1.5rem; align-items: start;">

                <div class="modal-left">
                    <div class="modal-thumb">
                        <img
                            id="modal-thumb-img"
                            alt="Detection thumbnail"
                            src=""
                            style="display:none; width:100%; height:auto; border-radius:12px; object-fit:cover;"
                        />
                    </div>
                </div>

                <div class="modal-right">
                    <div
                        class="modal-status-text"
                        id="modal-status-text"
                        style="font-size: 1.3rem; font-weight: 700; color: #6d4c41; margin-bottom: 0.5rem;"
                    >
                        Analyzing...
                    </div>

                    <div
                        class="modal-status-details"
                        id="modal-status-details"
                        style="font-size: 1rem; color: #8d6e63; margin-bottom: 1rem;"
                    ></div>

                    <div
                        id="modal-confidence-container"
                        style="display: none; padding: 1rem 0; border-top: 1px solid #d7ccc8; border-bottom: 1px solid #d7ccc8;"
                    >
                        <div
                            class="modal-confidence"
                            style="font-size: 0.9rem; color: #666; margin-bottom: 0.5rem; font-weight: 500;"
                        >
                            Detection Confidence
                        </div>

                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <div class="modal-confidence-bar" style="flex: 1;">
                                <div class="modal-confidence-fill" id="modal-confidence-fill"></div>
                            </div>

                            <span
                                id="modal-confidence-value"
                                style="font-weight: 700; color: #6d4c41; min-width: 40px; text-align: right;"
                            >
                                0%
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="modal-breed-info" class="modal-breed-info" style="display: none;">
            <h3 class="modal-breed-title" id="modal-breed-name">Breed Information</h3>

            <div class="modal-breed-grid">
                <div class="modal-breed-item">
                    <div class="modal-breed-label">Scientific Name</div>
                    <div class="modal-breed-value" id="modal-breed-scientific">N/A</div>
                </div>

                <div class="modal-breed-item">
                    <div class="modal-breed-label">Egg Production</div>
                    <div class="modal-breed-value" id="modal-breed-eggs">N/A</div>
                </div>

                <div class="modal-breed-item">
                    <div class="modal-breed-label">Mature Weight</div>
                    <div class="modal-breed-value" id="modal-breed-weight">N/A</div>
                </div>

                <div class="modal-breed-item">
                    <div class="modal-breed-label">Maturity Age</div>
                    <div class="modal-breed-value" id="modal-breed-age">N/A</div>
                </div>

                <div class="modal-breed-item">
                    <div class="modal-breed-label">Temperature</div>
                    <div class="modal-breed-value" id="modal-breed-temp">N/A</div>
                </div>

                <div class="modal-breed-item">
                    <div class="modal-breed-label">Humidity</div>
                    <div class="modal-breed-value" id="modal-breed-humidity">N/A</div>
                </div>
            </div>

            <p id="modal-breed-description" class="modal-breed-description"></p>
        </div>

        <div class="modal-buttons">
            <button
                type="button"
                class="modal-btn modal-btn-secondary"
                id="detectionModalCloseBottomButton"
            >
                Close
            </button>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="delete-modal">
    <div class="delete-modal-content">
        <h2 class="delete-modal-title" id="delete-modal-title">Delete Detection Record</h2>

        <p class="delete-modal-message" id="delete-modal-message">
            Are you sure you want to delete this detection record? This action cannot be undone.
        </p>

        <div class="delete-modal-buttons">
            <button class="delete-modal-btn cancel" onclick="closeDeleteModal()" type="button">
                Cancel
            </button>

            <button class="delete-modal-btn confirm" onclick="confirmDelete()" type="button">
                Yes, Delete
            </button>
        </div>
    </div>
</div>

<!-- TensorFlow.js and COCO-SSD Model -->
<script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@3"></script>
<script src="https://cdn.jsdelivr.net/npm/@tensorflow-models/coco-ssd@2"></script>

<script>
    let video = null;
    let canvas = null;
    let overlayCanvas = null;
    let overlayCtx = null;
    let stream = null;
    let cocoModel = null;
    let detectionInProgress = false;
    let autoDetectionActive = false;
    let autoDetectionInterval = null;
    let detectionHistory = [];
    let currentFilter = 'all';
    let deleteTargetId = null;
    let deleteMode = 'single';
    let lastClassification = null;
    let modalCooldownUntil = 0;

    function loadHistory() {
        const saved = localStorage.getItem('quail_detection_history');

        if (saved) {
            try {
                detectionHistory = JSON.parse(saved);
            } catch (e) {
                console.warn('Invalid detection history:', e);
                detectionHistory = [];
            }
        }

        renderHistory();
    }

    function saveHistory() {
        try {
            localStorage.setItem(
                'quail_detection_history',
                JSON.stringify(detectionHistory)
            );
        } catch (e) {
            try {
                const slim = detectionHistory.map((d, i) =>
                    i < detectionHistory.length - 10
                        ? Object.assign({}, d, { imageData: null })
                        : d
                );

                localStorage.setItem(
                    'quail_detection_history',
                    JSON.stringify(slim)
                );
            } catch (e2) {
                console.warn(
                    'History saved in memory only (localStorage full):',
                    e2
                );
            }
        }
    }

    function addToHistory(type, breedName, confidence, imageData) {
        const detection = {
            id: Date.now(),
            type: type,
            breedName: breedName || 'Unknown',
            confidence: confidence,
            imageData: imageData,
            timestamp: new Date().toISOString(),
            date: new Date().toLocaleString('en-US', {
                month: 'short',
                day: 'numeric',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            })
        };

        detectionHistory.unshift(detection);

        if (detectionHistory.length > 50) {
            detectionHistory = detectionHistory.slice(0, 50);
        }

        saveHistory();
        renderHistory();
    }

    function filterHistory(filter, event) {
        currentFilter = filter;

        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.classList.remove('active');
        });

        const target =
            (event && event.target)
                ? event.target
                : document.querySelector(
                    `.filter-btn[data-filter="${filter}"]`
                );

        if (target) {
            target.classList.add('active');
        }

        renderHistory();
    }

    function renderHistory() {
        const historyList = document.getElementById('history-list');

        const deleteAllBtn = document.getElementById('delete-all-btn');

        if (deleteAllBtn) {
            deleteAllBtn.disabled = detectionHistory.length === 0;
        }

        let filtered = detectionHistory;

        if (currentFilter !== 'all') {
            filtered = detectionHistory.filter(
                d => d.type === currentFilter
            );
        }

        if (filtered.length === 0) {
            historyList.innerHTML = `
                <div class="history-empty">
                    <div class="history-empty-icon">
                        <svg class="icon icon-search" width="36" height="36" aria-hidden="true">
                            <use xlink:href="#icon-search"/>
                        </svg>
                    </div>
                    <div class="history-empty-text">
                        No ${currentFilter === 'all' ? '' : currentFilter + ' '}detections yet
                    </div>
                </div>
            `;

            return;
        }

        historyList.innerHTML = filtered.map(detection => `
            <div class="history-item">

                <button
                    class="history-delete-btn"
                    onclick="deleteHistoryItem(${detection.id}, event)"
                    title="Delete"
                    type="button"
                >
                    <svg class="icon" width="14" height="14" aria-hidden="true">
                        <use xlink:href="#icon-cross"/>
                    </svg>
                </button>

                <div
                    onclick="viewHistoryDetail(${detection.id})"
                    style="display: flex; gap: 1rem; flex: 1; cursor: pointer;"
                >
                    ${
                        detection.imageData
                            ? `
                                <img
                                    src="${detection.imageData}"
                                    class="history-image"
                                    alt="Detection"
                                >
                            `
                            : `
                                <div
                                    class="history-image"
                                    style="display: flex; align-items: center; justify-content: center; color: #ccc; font-size: 2rem;"
                                >
                                    ${
                                        detection.type === 'quail'
                                            ? '<svg class="icon" width="28" height="28" aria-hidden="true"><use xlink:href="#icon-quail"/></svg>'
                                            : '<svg class="icon" width="28" height="28" aria-hidden="true"><use xlink:href="#icon-person"/></svg>'
                                    }
                                </div>
                            `
                    }

                    <div class="history-content">
                        <div class="history-breed">
                            ${detection.breedName}
                        </div>

                        <div class="history-type">
                            ${
                                detection.type === 'quail'
                                    ? `
                                        <span style="display:inline-flex;align-items:center;gap:6px;">
                                            <svg class="icon" width="16" height="16" aria-hidden="true">
                                                <use xlink:href="#icon-check"/>
                                            </svg>
                                            Quail Detected
                                        </span>
                                    `
                                    : `
                                        <span style="display:inline-flex;align-items:center;gap:6px;">
                                            <svg class="icon" width="16" height="16" aria-hidden="true">
                                                <use xlink:href="#icon-person"/>
                                            </svg>
                                            Human Detected
                                        </span>
                                    `
                            }
                        </div>

                        ${
                            detection.confidence > 0
                                ? `
                                    <div class="history-confidence">
                                        Confidence: ${(detection.confidence * 100).toFixed(1)}%
                                    </div>
                                `
                                : ''
                        }

                        <div class="history-date">
                            ${detection.date}
                        </div>
                    </div>
                </div>
            </div>
        `).join('');
    }

    function viewHistoryDetail(id) {
        const detection = detectionHistory.find(
            d => d.id === id
        );

        if (!detection) return;

        showDetectionResult(
            detection.type,
            detection.type === 'quail'
                ? 'Quail Detected!'
                : 'Human Detected!',
            `Breed: ${detection.breedName}`,
            detection.confidence,
            null,
            detection.imageData
        );

        if (detection.type === 'quail') {
            const breedMapping = {
                'Japanese Quail': 1,
                'Japanese Quail (Coturnix Japonica)': 1,
                'Japanese Coturnix Crossbreed (Taiwan)': 2,
                'Japanese Coturnix Crossbreed (Taiwan Brown Line)': 2,
                'Pharaoh Quail': 3
            };

            const breedId =
                breedMapping[detection.breedName] || 1;

            fetchAndShowBreedInfo(
                breedId,
                true
            );
        }
    }

    function deleteHistoryItem(id, event) {
        if (event) {
            event.stopPropagation();
        }

        deleteTargetId = id;
        deleteMode = 'single';

        setDeleteModalContent(
            'Delete Detection Record',
            'Are you sure you want to delete this detection record? This action cannot be undone.'
        );

        document
            .getElementById('deleteModal')
            .classList
            .add('show');
    }

    function clearAllHistory() {
        if (
            !detectionHistory ||
            detectionHistory.length === 0
        ) {
            return;
        }

        deleteTargetId = null;
        deleteMode = 'all';

        const count = detectionHistory.length;

        setDeleteModalContent(
            'Delete All Records',
            `Are you sure you want to delete all ${count} detection record${count > 1 ? 's' : ''}? This action cannot be undone.`
        );

        document
            .getElementById('deleteModal')
            .classList
            .add('show');
    }

    function setDeleteModalContent(title, message) {
        document.getElementById(
            'delete-modal-title'
        ).textContent = title;

        document.getElementById(
            'delete-modal-message'
        ).textContent = message;
    }

    function closeDeleteModal() {
        const modal =
            document.getElementById('deleteModal');

        if (modal) {
            modal.classList.remove('show');
        }

        deleteTargetId = null;
    }

    function confirmDelete() {
        if (deleteMode === 'all') {
            detectionHistory = [];

            saveHistory();
            renderHistory();
            closeDeleteModal();

        } else if (deleteTargetId) {

            detectionHistory =
                detectionHistory.filter(
                    d => d.id !== deleteTargetId
                );

            saveHistory();
            renderHistory();
            closeDeleteModal();
        }
    }

    async function initVideo() {
        video =
            document.getElementById('video-feed');

        canvas =
            document.getElementById('canvas-preview');

        overlayCanvas =
            document.getElementById('overlay-canvas');

        overlayCtx =
            overlayCanvas
                ? overlayCanvas.getContext('2d')
                : null;

        try {
            stream =
                await navigator.mediaDevices.getUserMedia({
                    video: {
                        facingMode: 'environment',
                        width: { ideal: 640 },
                        height: { ideal: 480 }
                    }
                });

            video.srcObject = stream;
            video.style.display = 'block';

            video.addEventListener(
                'loadedmetadata',
                () => {
                    try {
                        if (canvas) {
                            canvas.width =
                                video.videoWidth || 640;

                            canvas.height =
                                video.videoHeight || 480;
                        }

                        if (overlayCanvas) {
                            overlayCanvas.width =
                                video.videoWidth || 640;

                            overlayCanvas.height =
                                video.videoHeight || 480;

                            const rect =
                                video.getBoundingClientRect();

                            overlayCanvas.style.width =
                                rect.width + 'px';

                            overlayCanvas.style.height =
                                rect.height + 'px';

                            overlayCanvas.style.left =
                                (video.offsetLeft || 0) + 'px';

                            overlayCanvas.style.top =
                                (video.offsetTop || 0) + 'px';

                            overlayCanvas.style.pointerEvents =
                                'none';

                            overlayCanvas.style.display =
                                'block';
                        }

                    } catch (e) {
                        console.warn(
                            'Error sizing canvases:',
                            e
                        );
                    }
                }
            );

            document.getElementById(
                'camera-placeholder'
            ).style.display = 'none';

            const statusChip =
                document.getElementById('camera-status');

            if (statusChip) {
                statusChip.classList.remove(
                    'loading',
                    'error'
                );

                statusChip.classList.add(
                    'success'
                );

                statusChip.querySelector(
                    '.status-icon'
                ).innerHTML =
                    '<svg class="icon" width="16" height="16" aria-hidden="true"><use xlink:href="#icon-check"/></svg>';

                statusChip.querySelector(
                    '.status-text'
                ).textContent =
                    'Camera ready — Auto-detecting quails...';
            }

            startAutoDetection();

            document.getElementById(
                'start-btn'
            ).style.display = 'none';

            document.getElementById(
                'stop-btn'
            ).style.display = 'inline-block';

        } catch (err) {

            console.error(
                'Camera access error:',
                err
            );

            const statusChip =
                document.getElementById('camera-status');

            if (statusChip) {
                statusChip.classList.remove(
                    'loading',
                    'success'
                );

                statusChip.classList.add(
                    'error'
                );

                statusChip.querySelector(
                    '.status-icon'
                ).innerHTML =
                    '<svg class="icon" width="16" height="16" aria-hidden="true"><use xlink:href="#icon-cross"/></svg>';

                statusChip.querySelector(
                    '.status-text'
                ).textContent =
                    'Camera access denied';
            }

            alert(
                'Please allow camera access to use this feature.'
            );
        }
    }

    let cameraStarting = false;

    async function startCamera() {
        if (
            cameraStarting ||
            autoDetectionActive
        ) {
            return;
        }

        cameraStarting = true;

        const startBtn =
            document.getElementById('start-btn');

        if (startBtn) {
            startBtn.disabled = true;
        }

        const statusChip =
            document.getElementById('camera-status');

        if (statusChip) {
            statusChip.classList.add(
                'loading'
            );

            statusChip.classList.remove(
                'success',
                'error'
            );

            statusChip.querySelector(
                '.status-icon'
            ).innerHTML =
                '<svg class="icon" width="16" height="16" aria-hidden="true"><use xlink:href="#icon-question"/></svg>';

            statusChip.querySelector(
                '.status-text'
            ).textContent =
                'Loading AI model...';
        }

        try {
            cocoModel =
                await cocoSsd.load();

            if (statusChip) {
                statusChip.classList.remove(
                    'error'
                );

                statusChip.classList.add(
                    'loading'
                );

                statusChip.querySelector(
                    '.status-text'
                ).textContent =
                    'Model loaded, initializing camera...';
            }

            await initVideo();

        } catch (err) {

            console.error(
                'Model/Camera loading error:',
                err
            );

            if (statusChip) {
                statusChip.classList.remove(
                    'loading',
                    'success'
                );

                statusChip.classList.add(
                    'error'
                );

                statusChip.querySelector(
                    '.status-text'
                ).textContent =
                    `Error - ${err.message}. Click Start Camera to retry.`;
            }

        } finally {

            cameraStarting = false;

            if (startBtn) {
                startBtn.disabled = false;
            }
        }
    }

    function stopCamera() {
        stopAutoDetection();

        if (stream) {
            stream
                .getTracks()
                .forEach(track => track.stop());

            stream = null;
        }

        const videoElement =
            document.getElementById('video-feed');

        if (videoElement) {
            videoElement.style.display = 'none';
        }

        document.getElementById(
            'camera-placeholder'
        ).style.display = 'flex';

        try {
            if (
                overlayCtx &&
                overlayCanvas
            ) {
                overlayCtx.clearRect(
                    0,
                    0,
                    overlayCanvas.width,
                    overlayCanvas.height
                );
            }
        } catch (e) {
            console.warn(
                'Error clearing overlay:',
                e
            );
        }

        const statusChip =
            document.getElementById('camera-status');

        if (statusChip) {
            statusChip.classList.remove(
                'loading',
                'success'
            );

            statusChip.querySelector(
                '.status-icon'
            ).innerHTML =
                '<svg class="icon" width="16" height="16" aria-hidden="true"><use xlink:href="#icon-question"/></svg>';

            statusChip.querySelector(
                '.status-text'
            ).textContent =
                'Camera stopped';
        }

        document.getElementById(
            'start-btn'
        ).style.display = 'inline-block';

        document.getElementById(
            'stop-btn'
        ).style.display = 'none';
    }

    function startAutoDetection() {
        if (autoDetectionActive) {
            return;
        }

        autoDetectionActive = true;

        autoDetectionInterval =
            setInterval(() => {

                if (
                    autoDetectionActive &&
                    !detectionInProgress &&
                    !document.hidden
                ) {
                    performDetection();
                }

            }, 1500);
    }

    function stopAutoDetection() {
        autoDetectionActive = false;

        if (autoDetectionInterval) {
            clearInterval(
                autoDetectionInterval
            );

            autoDetectionInterval = null;
        }
    }

    function drawOverlay(predictions) {
        try {
            if (
                !overlayCanvas ||
                !overlayCtx ||
                !video
            ) {
                return;
            }

            overlayCtx.clearRect(
                0,
                0,
                overlayCanvas.width,
                overlayCanvas.height
            );

            if (
                !predictions ||
                predictions.length === 0
            ) {
                return;
            }

            const rect =
                video.getBoundingClientRect();

            const displayW =
                rect.width ||
                video.clientWidth ||
                overlayCanvas.width;

            const displayH =
                rect.height ||
                video.clientHeight ||
                overlayCanvas.height;

            const scaleX =
                overlayCanvas.width /
                displayW;

            const scaleY =
                overlayCanvas.height /
                displayH;

            const baseLine =
                Math.max(
                    2,
                    Math.round(
                        Math.min(
                            overlayCanvas.width,
                            overlayCanvas.height
                        ) / 240
                    )
                );

            let bestBirdIndex = -1;
            let bestBirdScore = 0;

            predictions.forEach(
                (pred, idx) => {

                    const cls =
                        (pred.class || '')
                            .toLowerCase();

                    const isBird =
                        cls.includes('bird') ||
                        cls.includes('quail') ||
                        cls.includes('chicken') ||
                        cls.includes('duck');

                    if (
                        isBird &&
                        (pred.score || 0) >
                        bestBirdScore
                    ) {
                        bestBirdScore =
                            pred.score || 0;

                        bestBirdIndex =
                            idx;
                    }
                }
            );

            predictions.forEach(
                (pred, idx) => {

                    let [
                        x,
                        y,
                        w,
                        h
                    ] = pred.bbox;

                    const score =
                        pred.score || 0;

                    x *= scaleX;
                    y *= scaleY;
                    w *= scaleX;
                    h *= scaleY;

                    let color =
                        '#e74c3c';

                    if (score >= 0.6) {
                        color =
                            '#27ae60';
                    } else if (score >= 0.4) {
                        color =
                            '#f1c40f';
                    }

                    overlayCtx.lineWidth =
                        baseLine;

                    overlayCtx.strokeStyle =
                        color;

                    overlayCtx.fillStyle =
                        color;

                    overlayCtx.globalAlpha =
                        1.0;

                    const radius =
                        Math.max(
                            4,
                            Math.round(
                                baseLine * 1.5
                            )
                        );

                    overlayCtx.beginPath();

                    overlayCtx.moveTo(
                        x + radius,
                        y
                    );

                    overlayCtx.lineTo(
                        x + w - radius,
                        y
                    );

                    overlayCtx.quadraticCurveTo(
                        x + w,
                        y,
                        x + w,
                        y + radius
                    );

                    overlayCtx.lineTo(
                        x + w,
                        y + h - radius
                    );

                    overlayCtx.quadraticCurveTo(
                        x + w,
                        y + h,
                        x + w - radius,
                        y + h
                    );

                    overlayCtx.lineTo(
                        x + radius,
                        y + h
                    );

                    overlayCtx.quadraticCurveTo(
                        x,
                        y + h,
                        x,
                        y + h - radius
                    );

                    overlayCtx.lineTo(
                        x,
                        y + radius
                    );

                    overlayCtx.quadraticCurveTo(
                        x,
                        y,
                        x + radius,
                        y
                    );

                    overlayCtx.closePath();
                    overlayCtx.stroke();

                    let label;

                    if (
                        idx === bestBirdIndex &&
                        lastClassification
                    ) {
                        label =
                            `${lastClassification.breedName} ${(lastClassification.confidence * 100).toFixed(0)}%`;
                    } else {
                        label =
                            `${pred.class} ${(score * 100).toFixed(0)}%`;
                    }

                    overlayCtx.font =
                        Math.max(
                            12,
                            Math.round(
                                baseLine * 6
                            )
                        ) +
                        'px Inter, Arial, sans-serif';

                    overlayCtx.textBaseline =
                        'top';

                    const padding =
                        Math.max(
                            4,
                            Math.round(
                                baseLine * 1.5
                            )
                        );

                    const textMetrics =
                        overlayCtx.measureText(
                            label
                        );

                    const labelWidth =
                        textMetrics.width +
                        padding * 2;

                    const labelHeight =
                        Math.max(
                            16,
                            Math.round(
                                baseLine * 5
                            )
                        );

                    let labelX = x;

                    let labelY =
                        y -
                        labelHeight -
                        6;

                    if (labelY < 0) {
                        labelY =
                            y + 6;
                    }

                    overlayCtx.globalAlpha =
                        0.9;

                    overlayCtx.fillStyle =
                        color;

                    overlayCtx.fillRect(
                        labelX,
                        labelY,
                        labelWidth,
                        labelHeight
                    );

                    overlayCtx.fillStyle =
                        '#ffffff';

                    overlayCtx.globalAlpha =
                        1.0;

                    overlayCtx.fillText(
                        label,
                        labelX + padding,
                        labelY +
                            Math.max(
                                2,
                                Math.round(
                                    baseLine / 2
                                )
                            )
                    );
                }
            );

        } catch (err) {
            console.error(
                'Overlay draw error:',
                err
            );
        }
    }

    function captureFrame(
        maxWidth,
        quality
    ) {
        const capCanvas =
            document.getElementById(
                'canvas-preview'
            );

        const ctx =
            capCanvas.getContext('2d');

        const vw =
            video.videoWidth || 640;

        const vh =
            video.videoHeight || 480;

        const scale =
            Math.min(
                1,
                maxWidth / vw
            );

        const w =
            Math.round(
                vw * scale
            );

        const h =
            Math.round(
                vh * scale
            );

        if (
            capCanvas.width !== w ||
            capCanvas.height !== h
        ) {
            capCanvas.width = w;
            capCanvas.height = h;
        }

        ctx.drawImage(
            video,
            0,
            0,
            w,
            h
        );

        return capCanvas.toDataURL(
            'image/jpeg',
            quality
        );
    }

    async function performDetection() {
        if (
            !cocoModel ||
            !video ||
            !video.videoWidth ||
            detectionInProgress
        ) {
            return;
        }

        detectionInProgress = true;

        try {
            const predictions =
                await cocoModel.detect(
                    video
                );

            const filteredPredictions =
                predictions.filter(
                    pred =>
                        pred.score >= 0.25
                );

            drawOverlay(
                filteredPredictions
            );

            if (
                filteredPredictions.length === 0
            ) {
                lastClassification = null;
                return;
            }

            const nowMs =
                Date.now();

            const modal =
                document.getElementById(
                    'detectionModal'
                );

            const suppressed =
                (
                    modal &&
                    modal.classList.contains(
                        'show'
                    )
                ) ||
                nowMs < modalCooldownUntil;

            if (suppressed) {
                return;
            }

            const imageData =
                captureFrame(
                    480,
                    0.7
                );

            const thumbData =
                captureFrame(
                    160,
                    0.5
                );

            const detectedClasses =
                filteredPredictions.map(
                    p =>
                        (p.class || '')
                            .toLowerCase()
                );

            const hasQuail =
                detectedClasses.some(
                    cls =>
                        cls.includes('bird') ||
                        cls.includes('quail') ||
                        cls.includes('chicken') ||
                        cls.includes('duck')
                );

            const hasHuman =
                detectedClasses.some(
                    cls =>
                        cls.includes('person') ||
                        cls.includes('human')
                );

            if (
                hasHuman &&
                !hasQuail
            ) {
                lastClassification =
                    null;

                let personScore = 0;

                filteredPredictions.forEach(
                    pred => {

                        const cls =
                            (pred.class || '')
                                .toLowerCase();

                        if (
                            (
                                cls.includes(
                                    'person'
                                ) ||
                                cls.includes(
                                    'human'
                                )
                            ) &&
                            (
                                pred.score || 0
                            ) > personScore
                        ) {
                            personScore =
                                pred.score || 0;
                        }
                    }
                );

                showDetectionResult(
                    'human',
                    'Human Detected',
                    'Please point the camera at a quail',
                    personScore,
                    null,
                    imageData
                );

                addToHistory(
                    'human',
                    'Human',
                    personScore,
                    thumbData
                );

                modalCooldownUntil =
                    Date.now() + 8000;

                return;
            }

            if (hasQuail) {

                const classificationResult =
                    await classifyBreedFromImage(
                        imageData
                    );

                modalCooldownUntil =
                    Date.now() + 8000;

                if (
                    classificationResult &&
                    classificationResult.success
                ) {

                    const predictedClass =
                        Number(
                            classificationResult.predicted_class
                        );

                    const confidence =
                        Number(
                            classificationResult.confidence
                        ) || 0;

                    const breedId =
                        predictedClass + 1;

                    const breedNames = {
                        0: 'Japanese Quail',
                        1: 'Japanese Coturnix Crossbreed (Taiwan)',
                        2: 'Pharaoh Quail'
                    };

                    const breedName =
                        breedNames[
                            predictedClass
                        ] ||
                        classificationResult.class_name ||
                        'Unknown Quail';

                    lastClassification = {
                        breedName:
                            breedName,

                        confidence:
                            confidence
                    };

                    showDetectionResult(
                        'quail',
                        'Quail Detected!',
                        `Breed: ${breedName}`,
                        confidence,
                        breedId,
                        imageData
                    );

                    addToHistory(
                        'quail',
                        breedName,
                        confidence,
                        thumbData
                    );

                    await fetchAndShowBreedInfo(
                        breedId,
                        true
                    );

                } else {

                    console.error(
                        'Quail classification failed:',
                        classificationResult
                    );

                    lastClassification =
                        null;

                    showDetectionResult(
                        'quail',
                        'Quail Detected!',
                        'Breed classification is currently unavailable.',
                        0,
                        null,
                        imageData
                    );
                }
            }

        } catch (err) {

            console.error(
                'Detection error:',
                err
            );

        } finally {

            detectionInProgress =
                false;
        }
    }

    async function classifyBreedFromImage(
        imageDataUrl
    ) {
        try {
            const response =
                await fetch(
                    imageDataUrl
                );

            const blob =
                await response.blob();

            const formData =
                new FormData();

            formData.append(
                'image',
                blob,
                'detection.jpg'
            );

            const classifyResponse =
                await fetch(
                    '/quail-detection/classify',
                    {
                        method: 'POST',

                        headers: {
                            'X-CSRF-TOKEN':
                                document.querySelector(
                                    'meta[name="csrf-token"]'
                                ).content
                        },

                        body:
                            formData
                    }
                );

            if (
                classifyResponse.ok
            ) {
                return await classifyResponse.json();
            }

            const errorText =
                await classifyResponse.text();

            console.error(
                'Classification failed with status',
                classifyResponse.status
            );

            console.error(
                'Error response:',
                errorText
            );

            return {
                success: false,
                error:
                    `Server error: ${classifyResponse.status}`
            };

        } catch (err) {

            console.error(
                'Classification error:',
                err
            );

            return {
                success: false,
                error:
                    err.message
            };
        }
    }

    function determineBreed(
        detectedClass,
        confidence
    ) {
        return 1;
    }

    async function fetchAndShowBreedInfo(
        breedId,
        showInModal = false
    ) {
        try {

            const response =
                await fetch(
                    `/quail-detection/breed/${breedId}`,
                    {
                        headers: {
                            'X-CSRF-TOKEN':
                                document.querySelector(
                                    'meta[name="csrf-token"]'
                                ).content
                        }
                    }
                );

            if (response.ok) {

                const data =
                    await response.json();

                const breed =
                    data.breed;

                if (showInModal) {

                    document.getElementById(
                        'modal-breed-name'
                    ).textContent =
                        breed.name;

                    document.getElementById(
                        'modal-breed-scientific'
                    ).textContent =
                        breed.scientific_name;

                    document.getElementById(
                        'modal-breed-eggs'
                    ).textContent =
                        breed.egg_production_rate;

                    document.getElementById(
                        'modal-breed-weight'
                    ).textContent =
                        breed.mature_weight;

                    document.getElementById(
                        'modal-breed-age'
                    ).textContent =
                        breed.maturity_age;

                    document.getElementById(
                        'modal-breed-temp'
                    ).textContent =
                        breed.optimal_temperature +
                        '°C';

                    document.getElementById(
                        'modal-breed-humidity'
                    ).textContent =
                        breed.optimal_humidity +
                        '%';

                    document.getElementById(
                        'modal-breed-description'
                    ).textContent =
                        breed.description;

                    document.getElementById(
                        'modal-breed-info'
                    ).style.display =
                        'block';

                } else {

                    document.getElementById(
                        'breed-name'
                    ).textContent =
                        breed.name;

                    document.getElementById(
                        'breed-scientific'
                    ).textContent =
                        breed.scientific_name;

                    document.getElementById(
                        'breed-eggs'
                    ).textContent =
                        breed.egg_production_rate;

                    document.getElementById(
                        'breed-weight'
                    ).textContent =
                        breed.mature_weight;

                    document.getElementById(
                        'breed-age'
                    ).textContent =
                        breed.maturity_age;

                    document.getElementById(
                        'breed-temp'
                    ).textContent =
                        breed.optimal_temperature +
                        '°C';

                    document.getElementById(
                        'breed-humidity'
                    ).textContent =
                        breed.optimal_humidity +
                        '%';

                    document.getElementById(
                        'breed-description'
                    ).textContent =
                        breed.description;

                    document.getElementById(
                        'breed-info'
                    ).classList.add(
                        'show'
                    );
                }
            }

        } catch (err) {

            console.error(
                'Error fetching breed info:',
                err
            );

            demoShowBreedInfo(
                breedId,
                showInModal
            );
        }
    }

    /*
     * FIXED MODAL OPEN FUNCTION
     *
     * Important:
     * - Removes any inline display:none left by the close function.
     * - Restores pointer-events.
     * - Places the modal above the page.
     * - Explicitly enables both close buttons.
     */
    function showDetectionResult(
        type,
        title,
        details,
        confidence = 0,
        breedId = null,
        imageData = null
    ) {
        const modal =
            document.getElementById(
                'detectionModal'
            );

        if (!modal) {
            return;
        }

        modal.style.display =
            'flex';

        modal.style.pointerEvents =
            'auto';

        modal.style.zIndex =
            '99999';

        modal.setAttribute(
            'aria-hidden',
            'false'
        );

        const thumbImg =
            document.getElementById(
                'modal-thumb-img'
            );

        if (thumbImg) {

            if (imageData) {

                thumbImg.src =
                    imageData;

                thumbImg.style.display =
                    'block';

            } else {

                thumbImg.src =
                    '';

                thumbImg.style.display =
                    'none';
            }
        }

        const statusTextEl =
            document.getElementById(
                'modal-status-text'
            );

        const breedInfo =
            document.getElementById(
                'modal-breed-info'
            );

        if (statusTextEl) {

            if (type === 'human') {

                statusTextEl.textContent =
                    title;

                statusTextEl.style.color =
                    '#c0392b';

                if (breedInfo) {
                    breedInfo.style.display =
                        'none';
                }

            } else if (
                type === 'quail'
            ) {

                statusTextEl.textContent =
                    title;

                statusTextEl.style.color =
                    '#27ae60';

            } else {

                statusTextEl.textContent =
                    title;

                statusTextEl.style.color =
                    '#7f8c8d';
            }
        }

        const detailsEl =
            document.getElementById(
                'modal-status-details'
            );

        if (detailsEl) {
            detailsEl.textContent =
                details;
        }

        const confidenceContainer =
            document.getElementById(
                'modal-confidence-container'
            );

        const confidenceValue =
            document.getElementById(
                'modal-confidence-value'
            );

        const confidenceFill =
            document.getElementById(
                'modal-confidence-fill'
            );

        if (confidence > 0) {

            if (confidenceContainer) {
                confidenceContainer.style.display =
                    'block';
            }

            if (confidenceValue) {
                confidenceValue.textContent =
                    (confidence * 100)
                        .toFixed(0) +
                    '%';
            }

            if (confidenceFill) {
                confidenceFill.style.width =
                    (confidence * 100) +
                    '%';
            }

        } else {

            if (confidenceContainer) {
                confidenceContainer.style.display =
                    'none';
            }

            if (confidenceFill) {
                confidenceFill.style.width =
                    '0%';
            }
        }

        if (
            typeof updateModalOverlayButtonState ===
            'function'
        ) {
            updateModalOverlayButtonState();
        }

        /*
         * Enable the modal BEFORE showing it.
         */
        modal.classList.add(
            'show'
        );

        /*
         * Explicitly restore button clickability.
         */
        const closeButton =
            document.getElementById(
                'detectionModalCloseButton'
            );

        const bottomCloseButton =
            document.getElementById(
                'detectionModalCloseBottomButton'
            );

        if (closeButton) {

            closeButton.disabled =
                false;

            closeButton.style.display =
                'flex';

            closeButton.style.visibility =
                'visible';

            closeButton.style.opacity =
                '1';

            closeButton.style.pointerEvents =
                'auto';

            closeButton.style.zIndex =
                '100002';
        }

        if (bottomCloseButton) {

            bottomCloseButton.disabled =
                false;

            bottomCloseButton.style.display =
                'inline-flex';

            bottomCloseButton.style.visibility =
                'visible';

            bottomCloseButton.style.opacity =
                '1';

            bottomCloseButton.style.pointerEvents =
                'auto';

            bottomCloseButton.style.zIndex =
                '100002';
        }

        if (
            type === 'quail' &&
            breedId
        ) {
            setCurrentBreedFromDetection(
                breedId
            );
        }

        saveDetectionRecord(
            type,
            confidence,
            breedId,
            imageData
        );
    }

    /*
     * FIXED CLOSE FUNCTION
     */
    function closeDetectionModalAndRefresh(
        refresh = false
    ) {
        const modal =
            document.getElementById(
                'detectionModal'
            );

        if (!modal) {
            return;
        }

        modal.classList.remove(
            'show'
        );

        modal.setAttribute(
            'aria-hidden',
            'true'
        );

        modal.style.display =
            'none';

        modal.style.pointerEvents =
            'none';

        /*
         * Clear cooldown so the next detection
         * can happen normally after closing.
         */
        if (refresh) {
            modalCooldownUntil =
                Date.now() + 1000;

            console.log(
                'Modal closed with explicit refresh requested'
            );
        }

        if (autoDetectionActive) {
            startAutoDetection();
        }
    }

    function closeDetectionModal() {
        closeDetectionModalAndRefresh(
            false
        );
    }

    function toggleOverlayFromModal() {
        try {

            if (!overlayCanvas) {

                overlayCanvas =
                    document.getElementById(
                        'overlay-canvas'
                    );

                overlayCtx =
                    overlayCanvas
                        ? overlayCanvas.getContext('2d')
                        : null;
            }

            const btn =
                document.getElementById(
                    'modal-toggle-overlay-btn'
                );

            if (
                !overlayCanvas ||
                !btn
            ) {
                return;
            }

            const currentlyHidden =
                overlayCanvas.style.display ===
                    'none' ||
                getComputedStyle(
                    overlayCanvas
                ).display ===
                    'none';

            const eyeSvg =
                '<svg class="icon" width="18" height="18" aria-hidden="true"><use xlink:href="#icon-eye"/></svg>';

            const eyeOffSvg =
                '<svg class="icon" width="18" height="18" aria-hidden="true"><use xlink:href="#icon-eye-off"/></svg>';

            if (currentlyHidden) {

                overlayCanvas.style.display =
                    'block';

                btn.innerHTML =
                    eyeOffSvg;

                btn.setAttribute(
                    'aria-label',
                    'Hide overlay'
                );

                btn.title =
                    'Hide overlay';

            } else {

                overlayCanvas.style.display =
                    'none';

                btn.innerHTML =
                    eyeSvg;

                btn.setAttribute(
                    'aria-label',
                    'Show overlay'
                );

                btn.title =
                    'Show overlay';
            }

        } catch (err) {

            console.error(
                'toggleOverlayFromModal error:',
                err
            );
        }
    }

    function updateModalOverlayButtonState() {
        const btn =
            document.getElementById(
                'modal-toggle-overlay-btn'
            );

        if (!btn) {
            return;
        }

        try {

            if (!overlayCanvas) {
                overlayCanvas =
                    document.getElementById(
                        'overlay-canvas'
                    );
            }

            const hidden =
                overlayCanvas &&
                (
                    overlayCanvas.style.display ===
                        'none' ||
                    getComputedStyle(
                        overlayCanvas
                    ).display ===
                        'none'
                );

            const eyeSvg =
                '<svg class="icon" width="18" height="18" aria-hidden="true"><use xlink:href="#icon-eye"/></svg>';

            const eyeOffSvg =
                '<svg class="icon" width="18" height="18" aria-hidden="true"><use xlink:href="#icon-eye-off"/></svg>';

            if (hidden) {

                btn.innerHTML =
                    eyeSvg;

                btn.setAttribute(
                    'aria-label',
                    'Show overlay'
                );

                btn.title =
                    'Show overlay';

            } else {

                btn.innerHTML =
                    eyeOffSvg;

                btn.setAttribute(
                    'aria-label',
                    'Hide overlay'
                );

                btn.title =
                    'Hide overlay';
            }

        } catch (err) {

            console.warn(
                'updateModalOverlayButtonState error:',
                err
            );

            btn.innerHTML =
                '<svg class="icon" width="18" height="18" aria-hidden="true"><use xlink:href="#icon-eye"/></svg>';

            btn.setAttribute(
                'aria-label',
                'Toggle overlay'
            );

            btn.title =
                'Toggle overlay';
        }
    }

    function clearResults() {
        closeDetectionModalAndRefresh(
            false
        );

        const breedInfo =
            document.getElementById(
                'modal-breed-info'
            );

        const confidenceContainer =
            document.getElementById(
                'modal-confidence-container'
            );

        if (breedInfo) {
            breedInfo.style.display =
                'none';
        }

        if (confidenceContainer) {
            confidenceContainer.style.display =
                'none';
        }
    }

    async function setCurrentBreedFromDetection(
        breedId
    ) {
        try {

            const response =
                await fetch(
                    '/api/breeds/set-from-detection',
                    {
                        method: 'POST',

                        headers: {
                            'Content-Type':
                                'application/json',

                            'X-CSRF-TOKEN':
                                document.querySelector(
                                    'meta[name="csrf-token"]'
                                )?.content || ''
                        },

                        body:
                            JSON.stringify({
                                breed_id:
                                    breedId
                            })
                    }
                );

            const data =
                await response.json();

            if (data.success) {

                console.log(
                    'Current breed updated from detection:',
                    data.breed.name
                );
            }

        } catch (err) {

            console.error(
                'Error setting breed from detection:',
                err
            );
        }
    }

    function demoShowBreedInfo(
        breedId,
        showInModal = false
    ) {
        const breedData = {

            1: {
                name:
                    'Japanese Quail (Coturnix Japonica)',

                scientific_name:
                    'Coturnix japonica',

                description:
                    'A popular quail breed known for its good egg and meat production.',

                egg_production_rate:
                    '280-300 eggs/year',

                mature_weight:
                    '100-120g',

                maturity_age:
                    '42-49 days',

                optimal_temperature:
                    '20-23°C',

                optimal_humidity:
                    '55-60%'
            },

            2: {
                name:
                    'Japanese Coturnix Crossbreed (Taiwan)',

                scientific_name:
                    'Coturnix japonica crossbreed',

                description:
                    'A Japanese Coturnix crossbreed associated with Taiwan, known for its hardy characteristics and suitability for quail production.',

                egg_production_rate:
                    '250-300 eggs/year',

                mature_weight:
                    '110-130g',

                maturity_age:
                    '42-49 days',

                optimal_temperature:
                    '20-23°C',

                optimal_humidity:
                    '55-60%'
            },

            3: {
                name:
                    'Pharaoh Quail',

                scientific_name:
                    'Coturnix japonica (Pharaoh strain)',

                description:
                    'A larger Coturnix quail strain commonly raised for meat production and general quail farming.',

                egg_production_rate:
                    '200-250 eggs/year',

                mature_weight:
                    '200-300g',

                maturity_age:
                    '42-49 days',

                optimal_temperature:
                    '20-23°C',

                optimal_humidity:
                    '55-60%'
            }
        };

        const breed =
            breedData[breedId] ||
            breedData[1];

        if (showInModal) {

            document.getElementById(
                'modal-breed-name'
            ).textContent =
                breed.name;

            document.getElementById(
                'modal-breed-scientific'
            ).textContent =
                breed.scientific_name;

            document.getElementById(
                'modal-breed-eggs'
            ).textContent =
                breed.egg_production_rate;

            document.getElementById(
                'modal-breed-weight'
            ).textContent =
                breed.mature_weight;

            document.getElementById(
                'modal-breed-age'
            ).textContent =
                breed.maturity_age;

            document.getElementById(
                'modal-breed-temp'
            ).textContent =
                breed.optimal_temperature;

            document.getElementById(
                'modal-breed-humidity'
            ).textContent =
                breed.optimal_humidity;

            document.getElementById(
                'modal-breed-description'
            ).textContent =
                breed.description;

            document.getElementById(
                'modal-breed-info'
            ).style.display =
                'block';

        } else {

            document.getElementById(
                'breed-name'
            ).textContent =
                breed.name;

            document.getElementById(
                'breed-scientific'
            ).textContent =
                breed.scientific_name;

            document.getElementById(
                'breed-eggs'
            ).textContent =
                breed.egg_production_rate;

            document.getElementById(
                'breed-weight'
            ).textContent =
                breed.mature_weight;

            document.getElementById(
                'breed-age'
            ).textContent =
                breed.maturity_age;

            document.getElementById(
                'breed-temp'
            ).textContent =
                breed.optimal_temperature;

            document.getElementById(
                'breed-humidity'
            ).textContent =
                breed.optimal_humidity;

            document.getElementById(
                'breed-description'
            ).textContent =
                breed.description;

            document.getElementById(
                'breed-info'
            ).classList.add(
                'show'
            );
        }
    }

    function saveDetectionRecord(
        detectionType,
        confidence,
        breedId = null,
        imageData = null
    ) {
        const payload = {
            detection_type:
                detectionType,

            confidence:
                confidence,

            quail_breed_id:
                breedId,

            location:
                'Camera Feed',

            detection_notes:
                '',

            image_data:
                imageData
        };

        fetch(
            '{{ route("quail-detection.save") }}',
            {
                method: 'POST',

                headers: {
                    'Content-Type':
                        'application/json',

                    'X-CSRF-TOKEN':
                        document.querySelector(
                            'meta[name="csrf-token"]'
                        ).content
                },

                body:
                    JSON.stringify(
                        payload
                    )
            }
        )
        .then(
            response =>
                response.json()
        )
        .then(
            data => {
                console.log(
                    'Detection saved:',
                    data
                );
            }
        )
        .catch(
            err =>
                console.error(
                    'Detection save error:',
                    err
                )
        );
    }

    /*
     * IMPORTANT MODAL CLICK HANDLERS
     *
     * These are attached directly after DOM loading.
     * This avoids depending only on inline onclick handlers.
     */
    document.addEventListener(
        'DOMContentLoaded',
        function() {

            loadHistory();

            const modal =
                document.getElementById(
                    'detectionModal'
                );

            const closeButton =
                document.getElementById(
                    'detectionModalCloseButton'
                );

            const bottomCloseButton =
                document.getElementById(
                    'detectionModalCloseBottomButton'
                );

            /*
             * X BUTTON
             */
            if (closeButton) {

                closeButton.addEventListener(
                    'click',
                    function(event) {

                        event.preventDefault();
                        event.stopPropagation();

                        closeDetectionModalAndRefresh(
                            false
                        );
                    }
                );
            }

            /*
             * CLOSE BUTTON
             */
            if (bottomCloseButton) {

                bottomCloseButton.addEventListener(
                    'click',
                    function(event) {

                        event.preventDefault();
                        event.stopPropagation();

                        closeDetectionModalAndRefresh(
                            true
                        );
                    }
                );
            }

            /*
             * MODAL BACKDROP CLICK
             */
            if (modal) {

                modal.addEventListener(
                    'click',
                    function(event) {

                        if (
                            event.target ===
                            modal
                        ) {
                            closeDetectionModalAndRefresh(
                                true
                            );
                        }
                    }
                );
            }

            /*
             * Prevent clicks inside modal content
             * from reaching the backdrop.
             */
            const modalContent =
                modal
                    ? modal.querySelector(
                        '.modal-content'
                    )
                    : null;

            if (modalContent) {

                modalContent.addEventListener(
                    'click',
                    function(event) {
                        event.stopPropagation();
                    }
                );
            }

            /*
             * ESC KEY ALSO CLOSES THE MODAL.
             */
            document.addEventListener(
                'keydown',
                function(event) {

                    if (
                        event.key ===
                        'Escape'
                    ) {

                        const currentModal =
                            document.getElementById(
                                'detectionModal'
                            );

                        if (
                            currentModal &&
                            currentModal.classList.contains(
                                'show'
                            )
                        ) {
                            closeDetectionModalAndRefresh(
                                false
                            );
                        }
                    }
                }
            );

            const deleteModal =
                document.getElementById(
                    'deleteModal'
                );

            if (deleteModal) {

                deleteModal.addEventListener(
                    'click',
                    function(event) {

                        if (
                            event.target ===
                            deleteModal
                        ) {
                            closeDeleteModal();
                        }
                    }
                );
            }
        }
    );

    window.addEventListener(
        'beforeunload',
        function() {

            stopAutoDetection();

            if (stream) {

                stream
                    .getTracks()
                    .forEach(
                        track =>
                            track.stop()
                    );
            }
        }
    );
</script>

@endsection