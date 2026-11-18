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

    /* Unified detection result modal (live + saved history) */
    #detectionModal .modal-content.result-modal-content {
        max-width: 940px;
        max-height: min(94vh, 960px);
        padding: 0;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        border-radius: 20px;
    }

    /* TOP: image only */
    .result-modal-image-top {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 auto;
        padding: 1.25rem 1.6rem;
        background:
            radial-gradient(circle at 12% 0%, rgba(255, 255, 255, 0.16), transparent 45%),
            linear-gradient(135deg, #4e342e 0%, #6d4c41 55%, #a1887f 100%);
    }

    .result-modal-image-frame {
        width: min(100%, 520px);
        max-height: 320px;
        border-radius: 16px;
        overflow: hidden;
        background: #efebe9;
        border: 2px solid rgba(255, 255, 255, 0.55);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.22);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .result-modal-image-frame img {
        width: 100%;
        max-height: 320px;
        object-fit: contain;
        display: block;
        background: #efebe9;
    }

    .result-image-empty {
        padding: 2.5rem 1rem;
        color: #9e8b82;
        font-size: 0.85rem;
    }

    #detectionModal .result-modal-image-close {
        position: absolute;
        top: 0.85rem;
        right: 1rem;
        width: 34px;
        height: 34px;
        border-radius: 50%;
        color: #fff;
        background: rgba(62, 39, 35, 0.45);
    }

    /* BOTTOM: all details */
    .result-details {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .result-modal-scroll {
        padding: 1.35rem 1.6rem 0.5rem;
        overflow-y: auto;
        min-height: 0;
        background: #fffdfa;
    }

    .result-status-banner {
        display: flex;
        align-items: flex-start;
        gap: 0.8rem;
        padding: 0.85rem 1rem;
        border-radius: 12px;
        margin-bottom: 1.25rem;
        border-left: 4px solid #a1887f;
        background: #f7f1ed;
    }

    .result-status-banner.success {
        border-left-color: #43a047;
        background: #eaf5ea;
    }

    .result-status-banner.uncertain {
        border-left-color: #ff9800;
        background: #fff8e1;
    }

    .result-status-banner.processing {
        border-left-color: #6d4c41;
        background: #efebe9;
    }

    .result-status-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: currentColor;
        margin-top: 0.35rem;
        flex: 0 0 auto;
        opacity: 0.75;
    }

    .result-status-banner strong {
        display: block;
        color: #4e342e;
        font-size: 0.95rem;
        margin-bottom: 0.2rem;
    }

    .result-status-banner p {
        margin: 0;
        color: #6d5c52;
        font-size: 0.84rem;
        line-height: 1.5;
    }

    .result-section {
        margin-bottom: 1.5rem;
    }

    .result-section-title {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 1rem;
        font-weight: 700;
        color: #6d4c41;
        margin: 0 0 0.85rem;
        padding-bottom: 0.45rem;
        border-bottom: 2px solid #f0e5df;
    }

    .result-section-title .icon {
        color: #a1887f;
        flex: 0 0 auto;
    }

    .result-section-title-upper {
        font-size: 0.85rem;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .result-breed-line {
        display: flex;
        align-items: baseline;
        flex-wrap: wrap;
        gap: 0.4rem;
    }

    .result-breed-line-label {
        font-size: 0.8rem;
        font-weight: 700;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: #8d6e63;
    }

    .result-breed-line-value {
        font-size: 1.15rem;
        font-weight: 700;
        color: #4e342e;
        word-break: break-word;
    }

    .result-breed-line-value.muted {
        color: #9e8b82;
        font-weight: 600;
    }

    .result-confidence-summary {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        font-size: 0.9rem;
        font-weight: 700;
        color: #6d4c41;
    }

    .result-breed-info-block {
        margin-bottom: 1.25rem;
    }

    .result-breed-info-block .result-section-title {
        margin-bottom: 0.75rem;
    }

    .result-image-wrap {
        display: grid;
        grid-template-columns: minmax(220px, 320px) minmax(0, 1fr);
        gap: 1rem;
        align-items: start;
        margin-bottom: 1rem;
    }

    .result-image-frame {
        border-radius: 14px;
        overflow: hidden;
        border: 2px solid #e0d6d2;
        background: #efebe9;
        min-height: 180px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .result-image-frame img {
        width: 100%;
        height: 100%;
        max-height: 240px;
        object-fit: contain;
        display: block;
        background: #efebe9;
    }

    .result-image-caption {
        font-size: 0.7rem;
        font-weight: 600;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: #8d6e63;
        margin-top: 0.4rem;
    }

    .result-facts {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 0.7rem;
    }

    .result-fact {
        background: #fff;
        border: 1px solid #e0d6d2;
        border-left: 3px solid #a1887f;
        border-radius: 10px;
        padding: 0.7rem 0.8rem;
        min-width: 0;
    }

    .result-fact.wide {
        grid-column: 1 / -1;
    }

    .result-fact-label {
        display: block;
        font-size: 0.68rem;
        font-weight: 700;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        color: #8d6e63;
        margin-bottom: 0.3rem;
    }

    .result-fact-value {
        display: block;
        font-size: 0.88rem;
        font-weight: 700;
        line-height: 1.45;
        color: #4e342e;
        word-break: break-word;
    }

    .result-fact-value.muted {
        color: #9e8b82;
        font-weight: 600;
    }

    .result-confidence-block {
        background: #fff;
        border: 1px solid #e0d6d2;
        border-radius: 12px;
        padding: 0.85rem 1rem;
    }

    .result-confidence-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        margin-bottom: 0.5rem;
    }

    .result-confidence-row span {
        font-size: 0.78rem;
        font-weight: 700;
        color: #8d6e63;
    }

    .result-confidence-row strong {
        font-size: 0.95rem;
        color: #4e342e;
    }

    .result-confidence-bar {
        height: 8px;
        border-radius: 999px;
        background: #e8ded9;
        overflow: hidden;
    }

    .result-confidence-fill {
        height: 100%;
        border-radius: 999px;
        background: linear-gradient(90deg, #a5d6a7 0%, #43a047 100%);
    }

    .result-note {
        margin: 0.85rem 0 0;
        padding: 0.7rem 0.85rem;
        border-radius: 10px;
        background: #fff8e1;
        border: 1px solid #ffe0b2;
        color: #7a5b2e;
        font-size: 0.78rem;
        line-height: 1.55;
    }

    .result-description {
        margin: 0.9rem 0 0;
        color: #5d4037;
        font-size: 0.86rem;
        line-height: 1.65;
    }

    .result-guide-care {
        display: grid;
        gap: 0.85rem;
        margin-top: 1rem;
    }

    .result-guide-subtitle {
        margin: 0 0 0.45rem;
        color: #6d4c41;
        font-size: 0.82rem;
        font-weight: 700;
    }

    .result-guide-list {
        margin: 0;
        padding-left: 1.15rem;
        color: #5d4037;
        font-size: 0.82rem;
        line-height: 1.6;
    }

    .result-guide-list li + li {
        margin-top: 0.3rem;
    }

    .result-modal-actions {
        flex: 0 0 auto;
        margin: 0;
        padding: 1rem 1.6rem 1.2rem;
        border-top: 1px solid #efebe9;
        background: #fff;
    }

    @media (max-width: 768px) {
        .result-modal-image-top {
            padding: 1rem;
        }

        .result-modal-image-frame,
        .result-modal-image-frame img {
            max-height: 240px;
        }

        .result-modal-scroll {
            padding: 1rem 1.1rem 0.5rem;
        }

        .result-facts {
            grid-template-columns: 1fr;
        }

        .result-breed-line {
            align-items: flex-start;
        }

        .result-confidence-summary {
            flex-wrap: wrap;
        }

        .modal-breed-grid {
            grid-template-columns: 1fr;
        }

        .result-modal-actions {
            padding: 0.85rem 1.1rem 1rem;
        }

        .result-modal-actions .modal-btn {
            flex: 1 1 140px;
        }
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

    /* Breed Information Modal */
    #breedInfoModal {
        z-index: 100010;
    }

    #breedInfoModal .modal-content {
        max-width: 920px;
        max-height: min(94vh, 940px);
        padding: 0;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        border-radius: 20px;
    }

    .breed-modal-top {
        display: grid;
        grid-template-columns: 220px minmax(0, 1fr);
        gap: 1.5rem;
        align-items: stretch;
        padding: 1.5rem;
        background:
            radial-gradient(circle at 12% 18%, rgba(255, 255, 255, 0.18), transparent 42%),
            linear-gradient(135deg, #4e342e 0%, #6d4c41 52%, #a1887f 100%);
        color: #fff;
    }

    .breed-modal-image {
        width: 100%;
        height: 100%;
        min-height: 210px;
        object-fit: cover;
        border-radius: 14px;
        border: 2px solid rgba(255, 255, 255, 0.55);
        background: #efebe9;
        display: block;
        box-shadow: 0 8px 22px rgba(0, 0, 0, 0.22);
    }

    .breed-modal-heading {
        display: flex;
        flex-direction: column;
        justify-content: center;
        min-width: 0;
    }

    .breed-modal-heading-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        margin-bottom: 0.5rem;
    }

    .breed-modal-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.14em;
        text-transform: uppercase;
        opacity: 0.88;
    }

    .breed-modal-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        margin-left: 0.5rem;
        padding: 0.2rem 0.55rem;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.16);
        border: 1px solid rgba(255, 255, 255, 0.3);
        font-size: 0.68rem;
        letter-spacing: 0.06em;
        text-transform: uppercase;
    }

    #breedInfoModal .modal-close {
        color: #fff;
        flex: 0 0 auto;
    }

    .breed-modal-title {
        font-size: 1.75rem;
        font-weight: 700;
        line-height: 1.22;
        margin: 0 0 0.35rem 0;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.18);
    }

    .breed-modal-scientific {
        font-size: 0.98rem;
        font-style: italic;
        opacity: 0.92;
        margin-bottom: 0.6rem;
    }

    .breed-modal-class {
        display: inline-block;
        font-size: 0.74rem;
        font-weight: 600;
        opacity: 0.82;
        margin-bottom: 0.35rem;
        word-break: break-word;
    }

    .breed-modal-confidence-wrap {
        margin-top: 0.65rem;
    }

    .breed-modal-confidence {
        display: inline-block;
        background: rgba(255, 255, 255, 0.18);
        border: 1px solid rgba(255, 255, 255, 0.35);
        border-radius: 999px;
        padding: 0.4rem 0.85rem;
        font-size: 0.85rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .breed-confidence-bar {
        width: 100%;
        max-width: 360px;
        height: 7px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.25);
        overflow: hidden;
    }

    .breed-confidence-fill {
        height: 100%;
        width: 0%;
        border-radius: 999px;
        background: linear-gradient(90deg, #a5d6a7 0%, #66bb6a 100%);
        transition: width 0.4s ease;
    }

    .breed-modal-scroll {
        overflow-y: auto;
        padding: 1.35rem 1.5rem 1rem;
        min-height: 0;
        background: #fffdfa;
    }

    .breed-quick-facts {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 0.75rem;
        margin-bottom: 1.35rem;
    }

    .breed-quick-card {
        background: #fff;
        border: 1px solid #e0d6d2;
        border-top: 3px solid #a1887f;
        border-radius: 12px;
        padding: 0.85rem;
        box-shadow: 0 3px 12px rgba(109, 76, 65, 0.06);
    }

    .breed-quick-label {
        font-size: 0.66rem;
        font-weight: 700;
        letter-spacing: 0.07em;
        text-transform: uppercase;
        color: #8d6e63;
        margin-bottom: 0.35rem;
    }

    .breed-quick-value {
        font-size: 0.84rem;
        font-weight: 700;
        line-height: 1.4;
        color: #4e342e;
    }

    .breed-overview {
        background: #fff;
        border: 1px solid #e0d6d2;
        border-left: 4px solid #6d4c41;
        border-radius: 12px;
        padding: 1rem 1.1rem;
        margin-bottom: 1rem;
    }

    .breed-overview-title {
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        color: #8d6e63;
        margin-bottom: 0.45rem;
    }

    .breed-overview p {
        margin: 0;
        font-size: 0.92rem;
        line-height: 1.7;
        color: #5d4037;
    }

    .breed-modal-note {
        margin: 0 0 1.4rem;
        padding: 0.75rem 0.9rem;
        border-radius: 10px;
        background: #fff8e1;
        border: 1px solid #ffe0b2;
        color: #7a5b2e;
        font-size: 0.8rem;
        line-height: 1.55;
    }

    #breedInfoSections {
        counter-reset: breed-section;
    }

    .breed-info-section {
        margin-bottom: 1.55rem;
    }

    .breed-info-section-title {
        font-size: 1rem;
        font-weight: 700;
        color: #6d4c41;
        margin: 0 0 0.8rem 0;
        padding-bottom: 0.4rem;
        border-bottom: 2px solid #f0e5df;
    }

    .breed-info-section-title::before {
        counter-increment: breed-section;
        content: counter(breed-section) ". ";
        color: #a1887f;
    }

    .breed-info-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 0.75rem;
    }

    .breed-info-item {
        background: #fff;
        border: 1px solid #e0d6d2;
        border-left: 3px solid #a1887f;
        border-radius: 10px;
        padding: 0.75rem 0.85rem;
    }

    .breed-info-item.wide {
        grid-column: 1 / -1;
    }

    .breed-info-label {
        font-size: 0.7rem;
        font-weight: 700;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        color: #8d6e63;
        margin-bottom: 0.3rem;
    }

    .breed-info-value {
        font-size: 0.875rem;
        line-height: 1.55;
        color: #4e342e;
    }

    .breed-bullet-list {
        list-style: none;
        margin: 0;
        padding: 0;
        display: grid;
        gap: 0.55rem;
    }

    .breed-bullet-list li {
        position: relative;
        padding-left: 1.45rem;
        font-size: 0.88rem;
        line-height: 1.55;
        color: #5d4037;
    }

    .breed-bullet-list li::before {
        content: "✓";
        position: absolute;
        left: 0;
        top: 0;
        width: 1.05rem;
        height: 1.05rem;
        border-radius: 50%;
        background: #efebe9;
        color: #6d4c41;
        font-size: 0.7rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .breed-callout {
        border-radius: 12px;
        padding: 0.95rem 1.05rem;
        font-size: 0.88rem;
        line-height: 1.65;
        color: #5d4037;
        border-left: 4px solid #6d4c41;
        background: #f7f1ed;
        margin-bottom: 1.4rem;
    }

    .breed-callout.compare {
        background: #fff8e1;
        border-left-color: #ff9800;
    }

    .breed-callout.model {
        background: #f3e8e3;
        border-left-color: #8d6e63;
    }

    .breed-callout strong {
        display: block;
        font-size: 0.72rem;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #8d6e63;
        margin-bottom: 0.35rem;
    }

    .breed-info-description {
        font-size: 0.92rem;
        line-height: 1.75;
        color: #5d4037;
        margin: 0 0 0.9rem;
    }

    .breed-info-description.extra {
        padding-top: 0.9rem;
        border-top: 1px dashed #d7ccc8;
        color: #6d5c52;
        font-size: 0.88rem;
    }

    .breed-modal-actions {
        margin: 0;
        padding: 1rem 1.5rem 1.25rem;
        border-top: 1px solid #efebe9;
        background: #fff;
    }

    .breed-modal-actions .modal-btn {
        min-width: 150px;
    }

    @media (max-width: 900px) {
        .breed-modal-top {
            grid-template-columns: 180px minmax(0, 1fr);
            gap: 1.1rem;
        }

        .breed-quick-facts {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 768px) {
        .breed-modal-top {
            grid-template-columns: 1fr;
            padding: 1.1rem;
            gap: 1rem;
        }

        .breed-modal-image {
            height: 180px;
            min-height: 0;
        }

        .breed-modal-title {
            font-size: 1.35rem;
        }

        .breed-modal-scroll {
            padding: 1rem 1.1rem 0.75rem;
        }

        .breed-info-grid {
            grid-template-columns: 1fr;
        }

        .breed-info-item.wide {
            grid-column: auto;
        }

        .breed-modal-actions {
            padding: 0.9rem 1.1rem 1rem;
        }

        .breed-modal-actions .modal-btn {
            flex: 1 1 140px;
        }
    }

    @media (max-width: 480px) {
        .breed-quick-facts {
            grid-template-columns: 1fr;
        }
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

                <button class="btn btn-secondary" id="upload-btn" onclick="document.getElementById('image-upload').click()" aria-label="Upload quail image" type="button">
                    <svg class="btn-icon" width="18" height="18" aria-hidden="true">
                        <use xlink:href="#icon-camera"/>
                    </svg>
                    Upload Image
                </button>

                <input
                    type="file"
                    id="image-upload"
                    accept="image/*"
                    style="display: none;"
                    aria-label="Choose a quail image to classify"
                />
            </div>

            <div class="detection-model-info">
                <svg class="icon icon-robot" width="18" height="18" aria-hidden="true">
                    <use xlink:href="#icon-robot"/>
                </svg>
                Quail is detected from the camera image; the breed is identified by plumage color (brown or gray/dark gray)
            </div>

            <div class="tutorial-section" style="margin-top: 1.5rem; padding: 1rem; background: linear-gradient(135deg, #fff8e1 0%, #fff3e0 100%); border-radius: 12px; border: 1px solid #ffe0b2;">
                <h3 style="color: #e65100; margin: 0 0 0.75rem 0; font-size: 1rem; font-weight: 700;">
                    <svg class="icon icon-book" width="18" height="18" aria-hidden="true" style="vertical-align: middle; margin-right: 6px;">
                        <use xlink:href="#icon-book"/>
                    </svg>
                    How to Use
                </h3>

                <ol style="margin: 0; padding-left: 1.25rem; color: #5d4037; font-size: 0.875rem; line-height: 1.6;">
                    <li><strong>Click "Start Camera"</strong> to enable your webcam, or <strong>Upload Image</strong> to use a saved photo</li>
                    <li><strong>Point the camera</strong> at your quails in good lighting</li>
                    <li><strong>Hold steady</strong> - the system auto-detects every 2 seconds</li>
                    <li><strong>Bird detected?</strong> The complete result appears automatically, including the breed identified from plumage color</li>
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

<!-- Detection Result Modal (shared by live detection and saved history) -->
<div id="detectionModal" class="modal" aria-hidden="true">
    <div class="modal-content result-modal-content" role="dialog" aria-modal="true" aria-labelledby="detection-result-heading">
        <div class="result-modal-image-top">
            <div class="result-modal-image-frame" id="detectionResultImage"></div>

            <button
                type="button"
                class="modal-close result-modal-image-close"
                id="detectionModalCloseButton"
                aria-label="Close result dialog"
                title="Close"
            >
                <svg class="icon" width="18" height="18" aria-hidden="true">
                    <use xlink:href="#icon-cross"/>
                </svg>
            </button>
        </div>

        <div class="result-modal-scroll" id="detectionResultContent"></div>

        <div class="modal-buttons result-modal-actions">
            <button
                type="button"
                class="modal-btn modal-btn-secondary"
                id="detectionModalCloseBottomButton"
            >
                Close
            </button>

            <button
                type="button"
                class="modal-btn modal-btn-primary"
                id="detectionModalScanAgainButton"
            >
                Scan Again
            </button>
        </div>
    </div>
</div>

<!-- Breed Information Modal -->
<div id="breedInfoModal" class="modal" aria-hidden="true">
    <div class="modal-content" role="dialog" aria-modal="true" aria-labelledby="breed-modal-title">
        <div class="breed-modal-top">
            <img
                id="breed-modal-image"
                class="breed-modal-image"
                alt="Captured or uploaded quail image"
                src=""
                style="display: none;"
            />

            <div class="breed-modal-heading">
                <div class="breed-modal-heading-row">
                    <span class="breed-modal-eyebrow">
                        <svg class="icon" width="15" height="15" aria-hidden="true">
                            <use xlink:href="#icon-quail"/>
                        </svg>
                        Breed Information
                        <span class="breed-modal-badge">
                            <svg class="icon" width="12" height="12" aria-hidden="true">
                                <use xlink:href="#icon-robot"/>
                            </svg>
                            AI result
                        </span>
                    </span>

                    <button
                        type="button"
                        class="modal-close"
                        id="breedModalCloseTopButton"
                        aria-label="Close breed information"
                        title="Close"
                    >
                        <svg class="icon" width="18" height="18" aria-hidden="true">
                            <use xlink:href="#icon-cross"/>
                        </svg>
                    </button>
                </div>

                <h2 class="breed-modal-title" id="breed-modal-title">Detected Breed</h2>
                <div class="breed-modal-scientific" id="breed-modal-scientific">Coturnix japonica</div>
                <div class="breed-modal-class" id="breed-modal-class">Model class: —</div>

                <div class="breed-modal-confidence-wrap">
                    <div class="breed-modal-confidence" id="breed-modal-confidence">
                        Prediction confidence: 0%
                    </div>
                    <div
                        class="breed-confidence-bar"
                        role="progressbar"
                        aria-label="Prediction confidence"
                        aria-valuemin="0"
                        aria-valuemax="100"
                        aria-valuenow="0"
                        id="breed-confidence-bar"
                    >
                        <div class="breed-confidence-fill" id="breed-confidence-fill"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="breed-modal-scroll">
            <div class="breed-quick-facts" id="breedQuickFacts"></div>

            <div class="breed-overview">
                <div class="breed-overview-title">Breed Overview</div>
                <p id="breedOverview"></p>
            </div>

            <p class="breed-modal-note">
                These values are general reference information for the breed, not
                measurements of the individual bird in the image. Prediction confidence
                is the model's score for this image and is not a guarantee of accuracy.
            </p>

            <div id="breedInfoSections"></div>

            <section class="breed-info-section">
                <h3 class="breed-info-section-title">Identification Notes</h3>
                <ul class="breed-bullet-list" id="breedIdentification"></ul>
            </section>

            <section class="breed-info-section">
                <h3 class="breed-info-section-title">Care Checklist</h3>
                <ul class="breed-bullet-list" id="breedCareChecklist"></ul>
            </section>

            <div class="breed-callout compare">
                <strong>Why the two detections stay separate</strong>
                <span id="breedComparisonNote"></span>
            </div>

            <div class="breed-callout model">
                <strong>How to read this result</strong>
                <span id="breedModelNote"></span>
            </div>
        </div>

        <div class="modal-buttons breed-modal-actions">
            <button
                type="button"
                class="modal-btn modal-btn-secondary"
                id="breedModalCloseButton"
            >
                Close
            </button>

            <button
                type="button"
                class="modal-btn modal-btn-primary"
                id="breedModalScanAgainButton"
            >
                Scan Again
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
    let cocoModelPromise = null;
    let detectionInProgress = false;
    let autoDetectionActive = false;
    let autoDetectionInterval = null;
    let detectionHistory = [];
    let currentFilter = 'all';
    let deleteTargetId = null;
    let deleteMode = 'single';
    let lastClassification = null;
    let modalCooldownUntil = 0;
    let classificationInProgress = false;
    let lastDetectionCompletedAt = 0;
    let lastDetectionRecord = null;
    let detectionDialogToken = 0;

    /*
     * Consecutive camera frames contain the same bird. This cooldown
     * suppresses repeat dialogs; it is not an artificial loading delay.
     */
    const DETECTION_COOLDOWN_MS = 3000;

    /*
     * Canonical reference data from config/quail.php. Old history records may
     * contain an older display name; the saved class key is still used to show
     * the current breed-information label without running a new prediction.
     */
    const QUAIL_BREED_REFERENCES = @json(config('quail.breed_references'));

    /*
     * Care details condensed from the Learn Book entries for
     * Japanese Quail / Coturnix Quail and Pharaoh Quail.
     */
    const LEARNBOOK_GUIDES = {
        japanese_quail_group: {
            title: 'Japanese Quail / Coturnix Quail Care Guide',
            overview: 'The most commonly raised quail in the Philippines. Hardier, fast-growing, and known for high egg production; suitable even for small-space farming.',
            facts: [
                ['Scientific Name', 'Coturnix japonica'],
                ['Primary Purpose', 'Eggs primarily; meat is also possible'],
                ['Laying Age', '35–45 days'],
                ['Annual Eggs', '250–300 eggs per hen per year'],
                ['Adult Weight', '120–180 g'],
                ['Lifespan', '2–3 years'],
                ['Daily Feed', '~20–25 g per bird per day'],
                ['Temperament', 'Active but easy to manage'],
                ['Color', 'Brown with speckles'],
                ['Climate', 'Adapts to hot, tropical climates']
            ],
            care: [
                {
                    title: 'Feeding & Nutrition',
                    items: [
                        'Starter feed for 0–3 weeks, grower feed for 3–5 weeks, then layer feed for laying birds.',
                        'Protein 18–24% with calcium for eggshell formation.',
                        'Clean drinking water should always be available.'
                    ]
                },
                {
                    title: 'Lighting for Egg Production',
                    items: [
                        'Provide 14–16 hours of light per day for continuous laying.'
                    ]
                },
                {
                    title: 'Housing & Cleanliness',
                    items: [
                        'Good ventilation; avoid overcrowding.',
                        'Protect birds from heat and rain.',
                        'Clean the enclosure regularly; a wire cage system is recommended.'
                    ]
                },
                {
                    title: 'Health & Disease Prevention',
                    items: [
                        'Watch for stress from heat or crowding, nutrient deficiency, and infections.',
                        'Keep the area clean, avoid sudden feed changes, and ensure enough water and ventilation.'
                    ]
                },
                {
                    title: 'Breeding',
                    items: [
                        'Mating ratio: 1 male to 3–4 females.',
                        'Incubation: 17–18 days at about 37.5 °C and 60–70% humidity.'
                    ]
                }
            ]
        },
        pharaoh_quail: {
            title: 'Pharaoh Quail Care Guide',
            overview: 'A gray to dark gray domestic Japanese quail raised for both eggs and meat. It adapts well to tropical climates.',
            facts: [
                ['Scientific Name', 'Coturnix japonica'],
                ['Primary Purpose', 'Eggs and meat (dual purpose)'],
                ['Laying Age', '35–45 days'],
                ['Annual Eggs', '200–280 eggs per hen per year'],
                ['Adult Weight', '150–200 g'],
                ['Lifespan', '2–3 years'],
                ['Daily Feed', '~20–30 g per bird per day'],
                ['Color', 'Gray / dark gray'],
                ['Climate', 'Suitable for tropical climates']
            ],
            care: [
                {
                    title: 'Feeding & Nutrition',
                    items: [
                        "Use starter, grower, and layer or finisher feeds depending on the bird's stage and purpose.",
                        'Protein 18–24%; calcium is especially important for layers.',
                        'Clean drinking water should always be available.'
                    ]
                },
                {
                    title: 'Lighting for Egg Production',
                    items: [
                        'Keep lighting consistent and nutrition adequate for steady laying.'
                    ]
                },
                {
                    title: 'Housing & Cleanliness',
                    items: [
                        'Provide good airflow and avoid crowding.',
                        'Protect birds from rain and heat.',
                        'Clean the enclosure regularly; wire cages are recommended.'
                    ]
                },
                {
                    title: 'Health & Disease Prevention',
                    items: [
                        'Watch for stress from heat or crowding, nutrient deficiency, and infections.',
                        'Maintain cleanliness, avoid sudden feed changes, and provide sufficient water and a good environment.'
                    ]
                },
                {
                    title: 'Breeding',
                    items: [
                        'Mating ratio: 1 male to 3–4 females.',
                        'Incubation: 17–18 days at about 37.5 °C and 60–70% humidity.'
                    ]
                }
            ]
        }
    };

    const BREED_INFO = {
        japanese_quail_group: {
            breedName: "Japanese Coturnix Quail (Taiwan Crossbreed)",
            scientificName: "Coturnix japonica",
            overview: "Japanese Coturnix Quail (Taiwan Crossbreed) is a domestic Japanese quail breeding line identified by its breeding background rather than by one fixed appearance. Because it is a crossbreed or breeding line, body fullness, feather markings, adult weight, and production performance can vary with the parent stock and with nutrition, lighting, and management. It is raised for both egg and meat production. The information in this modal describes the breed in general terms and is not a measurement of the individual bird in the image.",
            quickFacts: [
                ["Primary Purpose", "Egg and meat production"],
                ["Lifespan", "Several years under appropriate care; varies with health and management"],
                ["Sexual Maturity", "Commonly around 6–8 weeks"],
                ["Annual Eggs", "Varies with genetics, age, nutrition, lighting, and management"]
            ],
            sections: [
                {
                    title: "Basic Information",
                    fields: [
                        ["Breed Name", "Japanese Coturnix Quail (Taiwan Crossbreed)"],
                        ["Scientific Name", "Coturnix japonica"],
                        ["Classification", "Domestic Japanese quail crossbreed or breeding line"],
                        ["Primary Purpose", "Egg and meat production"],
                        ["Lifespan", "May live for several years under appropriate care; lifespan varies with health and management"],
                        ["Breed Context", "Identified by breeding background; physical traits and performance can vary with parent stock and management.", true]
                    ]
                },
                {
                    title: "Physical Characteristics",
                    fields: [
                        ["Body Shape", "Compact, oval-shaped body; body fullness varies among individuals"],
                        ["Feather Color", "May exhibit brown, cream, and dark feather markings depending on genetic background"],
                        ["Body Structure", "Small domestic quail with a compact body and short tail"],
                        ["Adult Weight", "Varies depending on breeding line, genetics, sex, and age"],
                        ["Distinctive Features", "Physical characteristics may differ depending on the parent lines used in crossbreeding"],
                        ["Variation Note", "Because this is a crossbreed or breeding line, appearance may differ between individuals even within the same category.", true]
                    ]
                },
                {
                    title: "Egg Production",
                    fields: [
                        ["Annual Egg Production", "Varies according to genetics, age, nutrition, lighting, and management"],
                        ["Egg Color", "Generally cream to pale-colored with brown or dark speckles"],
                        ["Typical Egg Weight", "Often approximately 9–13 grams in Japanese quail, although crossbred lines may differ"],
                        ["Sexual Maturity", "Commonly around 6–8 weeks, depending on genetics and rearing conditions"],
                        ["Production Note", "Egg output depends on genetics, age, nutrition, lighting, and management rather than one fixed number.", true]
                    ]
                },
                {
                    title: "Feeding Requirements",
                    fields: [
                        ["Feed Type", "Balanced commercial quail or game bird feed suitable for the bird's growth or production stage"],
                        ["Protein Requirements", "Depend on age, growth, and egg production requirements"],
                        ["Water Requirement", "Continuous access to clean drinking water"],
                        ["Feeding Recommendation", "Provide appropriate nutrients to maintain health, growth, and productivity"],
                        ["Stage Note", "Nutrient needs change with age, growth, and egg production; match the feed to the bird's current stage.", true]
                    ]
                },
                {
                    title: "Environmental and Care Requirements",
                    fields: [
                        ["Temperature", "Appropriate for age, feather development, and environmental conditions"],
                        ["Housing", "Clean, dry, secure, and properly ventilated enclosure"],
                        ["Lighting", "Appropriate and consistent lighting for the production stage"],
                        ["Health Monitoring", "Regularly observe feeding behavior, activity, feather condition, and signs of illness"],
                        ["Comfort Note", "Ventilation, cleanliness, and secure housing all contribute to health and consistent production.", true]
                    ]
                }
            ],
            identification: [
                "Look for a compact, oval-shaped body; fullness can differ between individuals.",
                "Feathering may combine brown, cream, and dark markings depending on genetic background.",
                "Because parent lines vary, appearance alone is not enough to confirm this breeding line; use the model result together with the source of the bird."
            ],
            careChecklist: [
                "Provide balanced commercial quail or game bird feed for the current growth or production stage.",
                "Keep clean drinking water available at all times.",
                "Use a clean, dry, secure, and properly ventilated enclosure.",
                "Maintain consistent lighting suitable for the production stage and watch feeding behavior, activity, feather condition, and signs of illness."
            ],
            comparisonNote: "Both Pharaoh Quail and Japanese Coturnix Quail (Taiwan Crossbreed) belong to the same species, Coturnix japonica. They are kept as separate detections because the first is a domestic variety associated with wild-type plumage, while the second is a crossbreed or breeding line identified by its breeding background.",
            modelNote: "The percentage shown is the model's confidence for this image. It is not a guarantee that the identification is correct, and it does not measure the individual bird. Confidence below the configured threshold is treated as Uncertain Detection and no breed is forced.",
            description: "Japanese Coturnix Quail (Taiwan Crossbreed) refers to a domestic Japanese quail breeding line identified by its breeding background. Its physical characteristics, body weight, and production performance may vary depending on the parent stock, genetics, nutrition, and management practices.",
            extendedDescription: "In practical terms, two Japanese Coturnix Quail (Taiwan Crossbreed) birds can look different from each other while still belonging to the same breeding-line category. Egg output, egg weight, and adult size should therefore be read as broad reference ranges rather than fixed expectations. Good records of parent stock, age, diet, lighting, and health are more useful for managing this line than appearance alone."
        },
        pharaoh_quail: {
            breedName: "Pharaoh Quail",
            scientificName: "Coturnix japonica",
            overview: "Pharaoh Quail is a commonly recognized domestic variety of Japanese quail associated with natural brown, streaked plumage. It is a domestic variety commonly associated with wild-type plumage and is raised for egg and meat production. The characteristics below describe the variety in general; individual birds can vary with genetics, sex, age, and feeding conditions.",
            quickFacts: [
                ["Primary Purpose", "Egg and meat production"],
                ["Lifespan", "Several years under appropriate care; varies with health and management"],
                ["Sexual Maturity", "Commonly around 6–8 weeks"],
                ["Annual Eggs", "Approximately 200–300 under favorable laying conditions"]
            ],
            sections: [
                {
                    title: "Basic Information",
                    fields: [
                        ["Breed Name", "Pharaoh Quail"],
                        ["Scientific Name", "Coturnix japonica"],
                        ["Classification", "Domestic Japanese quail variety commonly associated with wild-type plumage"],
                        ["Primary Purpose", "Egg and meat production"],
                        ["Lifespan", "May live for several years under appropriate care; lifespan varies with health and management"],
                        ["Breed Context", "A domestic Japanese quail variety commonly associated with wild-type plumage.", true]
                    ]
                },
                {
                    title: "Physical Characteristics",
                    fields: [
                        ["Body Shape", "Compact, oval-shaped body with a short tail"],
                        ["Feather Color", "Predominantly brown with dark streaks and mottled patterns"],
                        ["Body Structure", "Small, rounded terrestrial bird with short wings and a relatively compact build"],
                        ["Adult Weight", "Varies depending on genetics, sex, age, and feeding conditions"],
                        ["Distinctive Features", "Wild-type brown plumage with characteristic streaking and mottling"],
                        ["Variation Note", "Brown streaked plumage is characteristic, but individual variation with genetics, sex, and age is normal.", true]
                    ]
                },
                {
                    title: "Egg Production",
                    fields: [
                        ["Annual Egg Production", "Approximately 200–300 eggs under favorable laying conditions; actual output varies"],
                        ["Egg Color", "Usually cream to pale-colored with brown or dark speckles"],
                        ["Typical Egg Weight", "Approximately 9–13 grams"],
                        ["Sexual Maturity", "Commonly around 6–8 weeks of age"],
                        ["Production Note", "The 200–300 egg figure applies under favorable laying conditions; actual output varies.", true]
                    ]
                },
                {
                    title: "Feeding Requirements",
                    fields: [
                        ["Feed Type", "Nutritionally balanced quail or game bird feed appropriate for the bird's life stage"],
                        ["Protein Requirements", "Depend on age and production stage; growing birds generally require higher protein levels than mature birds"],
                        ["Water Requirement", "Continuous access to clean drinking water"],
                        ["Feeding Recommendation", "Provide suitable nutrients to support growth, maintenance, and egg production"],
                        ["Stage Note", "Growing birds generally need higher protein levels than mature birds, so match feed to age and production stage.", true]
                    ]
                },
                {
                    title: "Environmental and Care Requirements",
                    fields: [
                        ["Temperature", "Maintain environmental conditions appropriate for the bird's age and feather development"],
                        ["Housing", "Clean, dry, secure, and adequately ventilated enclosure"],
                        ["Lighting", "Consistent lighting appropriate for the production stage"],
                        ["Health Monitoring", "Observe feed intake, water consumption, activity, feather condition, and signs of illness"],
                        ["Comfort Note", "Clean, dry, secure, and well-ventilated housing supports health, feeding, and laying.", true]
                    ]
                }
            ],
            identification: [
                "Compact, oval-shaped body with a short tail.",
                "Predominantly brown plumage with dark streaks and mottled patterns.",
                "Wild-type brown streaking and mottling are characteristic, but individual variation is normal."
            ],
            careChecklist: [
                "Provide nutritionally balanced quail or game bird feed for the bird's life stage.",
                "Keep clean drinking water available at all times.",
                "Use a clean, dry, secure, and adequately ventilated enclosure.",
                "Provide consistent lighting and observe feed intake, water consumption, activity, feather condition, and signs of illness."
            ],
            comparisonNote: "Both Pharaoh Quail and Japanese Coturnix Quail (Taiwan Crossbreed) belong to the same species, Coturnix japonica. They are kept as separate detections because the first is a domestic variety associated with wild-type plumage, while the second is a crossbreed or breeding line identified by its breeding background.",
            modelNote: "The percentage shown is the model's confidence for this image. It is not a guarantee that the identification is correct, and it does not measure the individual bird. Confidence below the configured threshold is treated as Uncertain Detection and no breed is forced.",
            description: "Pharaoh Quail is a commonly recognized domestic variety of Japanese quail (Coturnix japonica) associated with natural brown, streaked plumage. It is raised for egg and meat production and is known for its relatively early sexual maturity.",
            extendedDescription: "Because this variety is associated with wild-type plumage, brown streaked feathering is a useful visual cue, but it is not unique to every Japanese quail. Egg production and sexual maturity are commonly in the ranges shown when birds are healthy and well managed. These figures are general reference points and can vary with genetics, sex, age, and feeding conditions."
        }
    };

    function escapeHtml(value) {
        return String(
            value === null || value === undefined ? '' : value
        )
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function finiteNumber(value) {
        const number = Number(value);
        return Number.isFinite(number) ? number : null;
    }

    function formatPercent(value) {
        const number = finiteNumber(value);

        if (number === null) {
            return 'Not available';
        }

        return (
            Math.min(1, Math.max(0, number)) * 100
        ).toFixed(1) + '%';
    }

    function formatDateTime(value) {
        const date = value ? new Date(value) : null;

        if (!date || Number.isNaN(date.getTime())) {
            return 'Not recorded';
        }

        const datePart = date.toLocaleDateString('en-US', {
            month: 'short',
            day: 'numeric',
            year: 'numeric'
        });

        const hours24 = date.getHours();
        const hours12 = hours24 % 12 || 12;
        const minutes = String(date.getMinutes()).padStart(2, '0');
        const meridiem = hours24 >= 12 ? 'PM' : 'AM';

        return (
            datePart +
            ' ' +
            String(hours12).padStart(2, '0') +
            minutes +
            meridiem
        );
    }

    function unavailableValue(value, fallback = 'Not recorded') {
        return value === null || value === undefined || value === ''
            ? fallback
            : value;
    }

    function isGenericBreedLabel(value) {
        if (value === null || value === undefined) {
            return true;
        }

        const label = String(value).trim().toLowerCase();

        return [
            '',
            'quail',
            'bird',
            'unknown',
            'uncertain detection',
            'not identified',
            'breed not identified',
            'n/a',
            'none'
        ].indexOf(label) !== -1;
    }

    /*
     * Replay the saved class reference, but use the current canonical label
     * for that trained class. No new prediction is made here.
     */
    function resolveBreedReference(record) {
        const source = record && typeof record === 'object'
            ? record
            : {};

        const recorded =
            source.breedReference &&
            typeof source.breedReference === 'object'
                ? source.breedReference
                : null;

        const key =
            source.breedReferenceKey ||
            (recorded ? recorded.reference_key : null) ||
            null;

        const canonical =
            key && QUAIL_BREED_REFERENCES[key]
                ? QUAIL_BREED_REFERENCES[key]
                : null;

        if (!canonical) {
            return recorded;
        }

        return Object.assign(
            {},
            recorded || {},
            canonical,
            { reference_key: key }
        );
    }

    function resolveRecordedBreedName(record) {
        const source = record && typeof record === 'object'
            ? record
            : {};
        const reference = resolveBreedReference(source);
        const candidates = [
            source.breedName,
            reference ? reference.common_name : null
        ];

        for (const candidate of candidates) {
            if (
                !isGenericBreedLabel(candidate)
            ) {
                return String(candidate).trim();
            }
        }

        return null;
    }

    function createDetectionRecord(options) {
        const optionsObject = options || {};
        const detectionType =
            optionsObject.detectionType || 'unknown';
        const classification =
            optionsObject.classification || null;
        const sensor = optionsObject.sensor || {
            temperature: null,
            humidity: null,
            sensorStatus: 'unavailable',
            sensorSource: null,
            sensorRecordedAt: null
        };

        let classificationStatus = 'not_applicable';
        let breedName = null;
        let breedReference = null;
        let breedReferenceKey = null;
        let modelClassName = null;
        let breedConfidence = null;
        let classificationMessage =
            'Object detected by the camera. Breed classification is not applicable.';

        if (detectionType === 'quail') {
            if (optionsObject.processing) {
                classificationStatus = 'processing';
                classificationMessage =
                    'Identifying the breed from the captured image.';
            } else if (
                classification &&
                classification.success === true &&
                classification.uncertain !== true &&
                classification.breed_reference
            ) {
                classificationStatus = 'identified';
                breedName = classification.breed_name || null;
                breedReference = classification.breed_reference;
                breedReferenceKey =
                    classification.breed_reference_key ||
                    classification.breed_reference.reference_key ||
                    null;
                modelClassName =
                    classification.model_class_name || null;
                breedConfidence =
                    finiteNumber(classification.confidence);
                classificationMessage =
                    'Breed identified from the captured image.';
            } else if (
                classification &&
                classification.success === true &&
                classification.uncertain === true
            ) {
                classificationStatus = 'uncertain';
                modelClassName =
                    classification.model_class_name || null;
                breedConfidence =
                    finiteNumber(classification.confidence);
                classificationMessage =
                    'The breed could not be confirmed from this image.';
            } else {
                classificationStatus = 'unavailable';
                classificationMessage =
                    'No breed was identified from this image.';
            }
        }

        return {
            id: optionsObject.id || (
                'det_' +
                Date.now() +
                '_' +
                Math.random().toString(36).slice(2, 8)
            ),
            detectionType: detectionType,
            source: optionsObject.source || 'camera',
            objectClass:
                optionsObject.objectClass || detectionType,
            objectDetectionResult:
                optionsObject.objectClass || detectionType,
            objectConfidence:
                finiteNumber(optionsObject.objectConfidence),
            detectedAt:
                optionsObject.detectedAt ||
                new Date().toISOString(),
            classificationStatus: classificationStatus,
            classificationMessage: classificationMessage,
            breedName: breedName,
            breedReference: breedReference,
            breedReferenceKey: breedReferenceKey,
            modelClassName: modelClassName,
            breedConfidence: breedConfidence,
            temperature: finiteNumber(sensor.temperature),
            humidity: finiteNumber(sensor.humidity),
            sensorStatus:
                sensor.sensorStatus || 'unavailable',
            sensorSource: sensor.sensorSource || null,
            sensorRecordedAt: sensor.sensorRecordedAt || null,
            imageData: optionsObject.imageData || null,
            saved: false,
            legacyRecord: false
        };
    }

    /*
     * Older localStorage entries do not contain structured classification
     * data. Show only what was actually recorded; never add a default breed.
     */
    function normalizeStoredDetection(raw) {
        const record = raw || {};
        const detectionType =
            record.detectionType || record.type || 'unknown';
        const recordedBreed =
            typeof record.breedName === 'string' &&
            record.breedName.trim() !== '' &&
            record.breedName !== 'Quail' &&
            record.breedName !== 'Uncertain Detection'
                ? record.breedName.trim()
                : null;

        const isStructured =
            Object.prototype.hasOwnProperty.call(
                record,
                'classificationStatus'
            );

        return {
            id: record.id || 'legacy_' + Date.now(),
            detectionType: detectionType,
            source: record.source || 'history',
            objectClass:
                record.objectClass ||
                record.objectDetectionResult ||
                recordedBreed ||
                detectionType,
            objectDetectionResult:
                record.objectDetectionResult ||
                record.objectClass ||
                detectionType,
            objectConfidence: finiteNumber(
                record.objectConfidence !== undefined
                    ? record.objectConfidence
                    : record.confidence
            ),
            detectedAt:
                record.detectedAt || record.timestamp || null,
            classificationStatus: isStructured
                ? (record.classificationStatus || 'unavailable')
                : (detectionType === 'quail'
                    ? 'legacy'
                    : 'not_applicable'),
            classificationMessage: isStructured
                ? (record.classificationMessage ||
                    'Saved detection result.')
                : (detectionType === 'quail'
                    ? 'This record was saved before structured classification data was available. Only the recorded values are shown.'
                    : 'Object detection record.'),
            breedName: isStructured
                ? (record.breedName || null)
                : recordedBreed,
            breedReference: isStructured
                ? (record.breedReference || null)
                : null,
            breedReferenceKey: isStructured
                ? (record.breedReferenceKey || null)
                : null,
            modelClassName: isStructured
                ? (record.modelClassName || null)
                : null,
            breedConfidence: finiteNumber(
                record.breedConfidence
            ),
            temperature: finiteNumber(record.temperature),
            humidity: finiteNumber(record.humidity),
            sensorStatus:
                record.sensorStatus || 'unavailable',
            sensorSource: record.sensorSource || null,
            sensorRecordedAt:
                record.sensorRecordedAt || null,
            imageData: record.imageData || null,
            saved: true,
            legacyRecord: !isStructured
        };
    }

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

    function addDetectionToHistory(record) {
        if (!record) {
            return;
        }

        const storedRecord = Object.assign({}, record, {
            saved: true
        });

        detectionHistory = detectionHistory.filter(
            item => String(item.id) !== String(storedRecord.id)
        );

        detectionHistory.unshift(storedRecord);

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
                d => (d.detectionType || d.type) === currentFilter
            );
        }

        if (!historyList) {
            return;
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

        historyList.innerHTML = filtered.map(rawDetection => {
            const detection = normalizeStoredDetection(rawDetection);
            const recordedId = escapeHtml(detection.id);
            const recordedBreedName =
                resolveRecordedBreedName(detection);
            const displayName =
                recordedBreedName ||
                detection.objectClass ||
                'Detection';
            const statusLabel =
                detection.classificationStatus === 'identified'
                    ? (recordedBreedName
                        ? 'Breed identified'
                        : 'Breed Not Identified')
                    : detection.classificationStatus === 'uncertain'
                        ? 'Uncertain classification'
                        : detection.classificationStatus === 'unavailable'
                            ? 'Breed Not Identified'
                            : detection.classificationStatus === 'legacy'
                                ? 'Legacy record'
                                : 'Object detection';

            return `
            <div class="history-item">
                <button
                    class="history-delete-btn"
                    onclick="deleteHistoryItem('${recordedId}', event)"
                    title="Delete"
                    type="button"
                >
                    <svg class="icon" width="14" height="14" aria-hidden="true">
                        <use xlink:href="#icon-cross"/>
                    </svg>
                </button>

                <div
                    onclick="viewHistoryDetail('${recordedId}')"
                    style="display: flex; gap: 1rem; flex: 1; cursor: pointer;"
                >
                    ${
                        detection.imageData
                            ? `
                                <img
                                    src="${escapeHtml(detection.imageData)}"
                                    class="history-image"
                                    alt="Saved detection"
                                >
                            `
                            : `
                                <div
                                    class="history-image"
                                    style="display: flex; align-items: center; justify-content: center; color: #ccc; font-size: 2rem;"
                                >
                                    <svg class="icon" width="28" height="28" aria-hidden="true">
                                        <use xlink:href="#icon-quail"/>
                                    </svg>
                                </div>
                            `
                    }

                    <div class="history-content">
                        <div class="history-breed">
                            ${escapeHtml(displayName)}
                        </div>

                        <div class="history-type">
                            ${escapeHtml(statusLabel)}
                        </div>

                        ${
                            finiteNumber(detection.objectConfidence) !== null
                                ? `
                                    <div class="history-confidence">
                                        Detection: ${formatPercent(detection.objectConfidence)}
                                    </div>
                                `
                                : ''
                        }

                        <div class="history-date">
                            ${escapeHtml(formatDateTime(detection.detectedAt))}
                        </div>
                    </div>
                </div>
            </div>
        `;
        }).join('');
    }

    function viewHistoryDetail(id) {
        const stored = detectionHistory.find(
            d => String(d.id) === String(id)
        );

        if (!stored) {
            return;
        }

        /*
         * History is replayed from the saved record only. No new
         * prediction and no default breed substitution.
         */
        openDetectionResult(
            normalizeStoredDetection(stored),
            { save: false }
        );
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
                    d =>
                        String(d.id) !==
                        String(deleteTargetId)
                );

            saveHistory();
            renderHistory();
            closeDeleteModal();
        }
    }

    /*
     * Load the detector once and reuse the promise. The camera is started
     * independently so the Start button reacts immediately.
     */
    function ensureCocoModel() {
        if (cocoModel) {
            return Promise.resolve(cocoModel);
        }

        if (typeof cocoSsd === 'undefined') {
            return Promise.reject(
                new Error('COCO-SSD detector is not available yet.')
            );
        }

        if (!cocoModelPromise) {
            cocoModelPromise = cocoSsd
                .load()
                .then(model => {
                    cocoModel = model;
                    return model;
                })
                .catch(error => {
                    cocoModelPromise = null;
                    throw error;
                });
        }

        return cocoModelPromise;
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
                    'Camera ready — starting detector...';
            }

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

            throw err;
        }
    }

    let cameraStarting = false;

    async function startCamera() {
        /*
         * autoDetectionActive can still be true after an upload, so the
         * camera check must be based on the actual stream.
         */
        if (cameraStarting || stream) {
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
                'Starting camera...';
        }

        try {
            /*
             * Kick off both tasks immediately. getUserMedia runs on the
             * user gesture, so the permission prompt appears right away
             * instead of after the detector has finished downloading.
             */
            const videoReady = initVideo();
            const modelReady = ensureCocoModel();

            await videoReady;

            if (statusChip) {
                statusChip.querySelector(
                    '.status-text'
                ).textContent =
                    'Camera ready — loading detector...';
            }

            try {
                await modelReady;

                startAutoDetection();

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

            } catch (modelError) {
                console.error(
                    'AI detector load error:',
                    modelError
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
                        'Camera ready — detector unavailable. Refresh to retry.';
                }
            }

        } catch (err) {

            console.error(
                'Camera start error:',
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
        // Do not create duplicate intervals, but recover if the
        // interval was stopped while the page is still active.
        if (
            autoDetectionActive &&
            autoDetectionInterval
        ) {
            return;
        }

        autoDetectionActive = true;

        if (autoDetectionInterval) {
            clearInterval(
                autoDetectionInterval
            );
            autoDetectionInterval = null;
        }

        autoDetectionInterval =
            setInterval(() => {

                if (
                    autoDetectionActive &&
                    !detectionInProgress &&
                    !document.hidden
                ) {
                    performDetection();
                }

            }, 500);
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

    // Display the actual object-detection score without inflating it.
    function getDisplayedDetectionConfidence(score) {
        const raw = Number(score) || 0;
        return Math.min(1, Math.max(0, raw));
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

                    const displayedConfidence =
                        getDisplayedDetectionConfidence(
                            score
                        );

                    if (
                        idx === bestBirdIndex &&
                        lastClassification
                    ) {
                        label =
                            `${lastClassification.breedName} ${(displayedConfidence * 100).toFixed(0)}%`;
                    } else {
                        label =
                            `${pred.class} ${(displayedConfidence * 100).toFixed(0)}%`;
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

            const breedModal =
                document.getElementById(
                    'breedInfoModal'
                );

            const suppressed =
                (
                    modal &&
                    modal.classList.contains(
                        'show'
                    )
                ) ||
                (
                    breedModal &&
                    breedModal.classList.contains(
                        'show'
                    )
                ) ||
                classificationInProgress ||
                nowMs < modalCooldownUntil ||
                nowMs <
                    lastDetectionCompletedAt +
                        DETECTION_COOLDOWN_MS;

            if (suppressed) {
                return;
            }

            const imageData =
                captureFrame(
                    480,
                    0.7
                );

            const detectedAt =
                new Date().toISOString();

            // Always use the highest-confidence detected object.
            // This makes ANY COCO-SSD object trigger the modal immediately.
            const bestPrediction =
                filteredPredictions.reduce(
                    (best, current) =>
                        (
                            current.score || 0
                        ) > (
                            best.score || 0
                        )
                            ? current
                            : best
                );

            const detectedClass =
                (bestPrediction.class || 'Object')
                    .trim();

            const detectedClassLower =
                detectedClass.toLowerCase();

            const rawDetectorConfidence =
                bestPrediction.score || 0;

            const detectorConfidence =
                getDisplayedDetectionConfidence(
                    rawDetectorConfidence
                );

            const isBird =
                detectedClassLower.includes('bird') ||
                detectedClassLower.includes('quail') ||
                detectedClassLower.includes('chicken') ||
                detectedClassLower.includes('duck');

            const isHuman =
                detectedClassLower.includes('person') ||
                detectedClassLower.includes('human');

            /*
             * BIRD:
             * The detector only reports bird presence. The color-rule
             * classifier runs while the dialog already shows an honest
             * processing state with no invented breed values.
             */
            if (isBird) {
                lastClassification = null;
                classificationInProgress = true;

                const processingRecord =
                    createDetectionRecord({
                        detectionType: 'quail',
                        objectClass: detectedClass,
                        objectConfidence:
                            detectorConfidence,
                        imageData: imageData,
                        detectedAt: detectedAt,
                        source: 'camera',
                        processing: true
                    });

                openDetectionResult(
                    processingRecord,
                    { save: false }
                );

                /*
                 * Capture the token AFTER opening the processing modal
                 * because openDetectionResult() increments the token.
                 * Otherwise the final result would never replace the modal.
                 */
                const dialogToken =
                    detectionDialogToken;

                modalCooldownUntil =
                    Date.now() + 10000;

                try {
                    const classificationResult =
                        await classifyBreedFromImage(
                            imageData
                        );

                    const finalRecord =
                        createDetectionRecord({
                            detectionType: 'quail',
                            objectClass:
                                detectedClass,
                            objectConfidence:
                                detectorConfidence,
                            classification:
                                classificationResult,
                            imageData: imageData,
                            detectedAt: detectedAt,
                            source: 'camera'
                        });

                    if (
                        dialogToken ===
                        detectionDialogToken
                    ) {
                        openDetectionResult(
                            finalRecord,
                            { save: true }
                        );
                    } else {
                        persistDetectionResult(
                            finalRecord
                        );
                    }

                } catch (error) {
                    console.error(
                        'Detection finalization failed:',
                        error
                    );

                    const fallbackRecord =
                        createDetectionRecord({
                            detectionType: 'quail',
                            objectClass:
                                detectedClass,
                            objectConfidence:
                                detectorConfidence,
                            classification: {
                                success: false
                            },
                            imageData: imageData,
                            detectedAt: detectedAt,
                            source: 'camera'
                        });

                    if (
                        dialogToken ===
                        detectionDialogToken
                    ) {
                        openDetectionResult(
                            fallbackRecord,
                            { save: true }
                        );
                    } else {
                        persistDetectionResult(
                            fallbackRecord
                        );
                    }

                } finally {
                    classificationInProgress =
                        false;

                    lastDetectionCompletedAt =
                        Date.now();

                    modalCooldownUntil =
                        lastDetectionCompletedAt +
                        DETECTION_COOLDOWN_MS;
                }

                return;
            }

            /*
             * HUMAN:
             * No breed classification is attempted for a person.
             */
            if (isHuman) {
                lastClassification = null;

                const record =
                    createDetectionRecord({
                        detectionType: 'human',
                        objectClass: detectedClass,
                        objectConfidence:
                            detectorConfidence,
                        imageData: imageData,
                        detectedAt: detectedAt,
                        source: 'camera'
                    });

                openDetectionResult(
                    record,
                    { save: true }
                );

                lastDetectionCompletedAt =
                    Date.now();

                modalCooldownUntil =
                    lastDetectionCompletedAt +
                    DETECTION_COOLDOWN_MS;

                return;
            }

            /*
             * ANY OTHER OBJECT:
             * No breed classification is attempted.
             */
            lastClassification = null;

            const objectRecord =
                createDetectionRecord({
                    detectionType: 'object',
                    objectClass: detectedClass,
                    objectConfidence:
                        detectorConfidence,
                    imageData: imageData,
                    detectedAt: detectedAt,
                    source: 'camera'
                });

            openDetectionResult(
                objectRecord,
                { save: true }
            );

            lastDetectionCompletedAt =
                Date.now();

            modalCooldownUntil =
                lastDetectionCompletedAt +
                DETECTION_COOLDOWN_MS;

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
        const abortController =
            new AbortController();

        let classifyTimeoutId = null;

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

            /*
             * Keep upload and camera breed classification within the
             * requested three-second response window.
             */
            classifyTimeoutId = setTimeout(
                () => abortController.abort(),
                3000
            );

            const classifyResponse =
                await fetch(
                    '{{ route("quail-detection.classify") }}',
                    {
                        method: 'POST',

                        headers: {
                            'X-CSRF-TOKEN':
                                document.querySelector(
                                    'meta[name="csrf-token"]'
                                ).content
                        },

                        body:
                            formData,

                        signal:
                            abortController.signal
                    }
                );

            clearTimeout(classifyTimeoutId);
            classifyTimeoutId = null;

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
            if (classifyTimeoutId !== null) {
                clearTimeout(classifyTimeoutId);
            }

            console.error(
                'Classification error:',
                err
            );

            return {
                success: false,
                error:
                    err.name === 'AbortError'
                        ? 'Classification timed out.'
                        : err.message
            };
        }
    }

    function openBreedInfoModal(
        classLabel,
        breedName,
        confidence,
        imageData
    ) {
        const info =
            BREED_INFO[classLabel] ||
            Object.values(BREED_INFO).find(
                entry => entry.breedName === breedName
            );

        if (!info) {
            console.error(
                'No breed information found for:',
                classLabel,
                breedName
            );
            return;
        }

        const modal =
            document.getElementById('breedInfoModal');

        if (!modal) {
            return;
        }

        const confidencePercent =
            Math.min(
                100,
                Math.max(
                    0,
                    Number(confidence) * 100
                )
            );

        document.getElementById(
            'breed-modal-title'
        ).textContent = info.breedName;

        document.getElementById(
            'breed-modal-scientific'
        ).textContent = info.scientificName;

        document.getElementById(
            'breed-modal-class'
        ).textContent =
            'Model class: ' + (classLabel || '—');

        document.getElementById(
            'breed-modal-confidence'
        ).textContent =
            'Prediction confidence: ' +
            confidencePercent.toFixed(1) +
            '%';

        const confidenceFill =
            document.getElementById(
                'breed-confidence-fill'
            );

        if (confidenceFill) {
            confidenceFill.style.width =
                confidencePercent + '%';
        }

        const confidenceBar =
            document.getElementById(
                'breed-confidence-bar'
            );

        if (confidenceBar) {
            confidenceBar.setAttribute(
                'aria-valuenow',
                confidencePercent.toFixed(0)
            );
        }

        const image =
            document.getElementById(
                'breed-modal-image'
            );

        if (image && imageData) {
            image.src = imageData;
            image.style.display = 'block';
        } else if (image) {
            image.removeAttribute('src');
            image.style.display = 'none';
        }

        const quickFacts =
            document.getElementById(
                'breedQuickFacts'
            );

        if (quickFacts) {
            quickFacts.innerHTML = '';

            info.quickFacts.forEach(fact => {
                const card =
                    document.createElement('div');

                card.className =
                    'breed-quick-card';

                const label =
                    document.createElement('div');

                label.className =
                    'breed-quick-label';

                label.textContent = fact[0];

                const value =
                    document.createElement('div');

                value.className =
                    'breed-quick-value';

                value.textContent = fact[1];

                card.appendChild(label);
                card.appendChild(value);
                quickFacts.appendChild(card);
            });
        }

        const overview =
            document.getElementById(
                'breedOverview'
            );

        if (overview) {
            overview.textContent =
                info.overview;
        }

        const sections =
            document.getElementById(
                'breedInfoSections'
            );

        if (sections) {
            sections.innerHTML = '';

            info.sections.forEach(section => {
                const sectionEl =
                    document.createElement('section');

                sectionEl.className =
                    'breed-info-section';

                const heading =
                    document.createElement('h3');

                heading.className =
                    'breed-info-section-title';

                heading.textContent =
                    section.title;

                const grid =
                    document.createElement('div');

                grid.className =
                    'breed-info-grid';

                section.fields.forEach(field => {
                    const item =
                        document.createElement('div');

                    item.className =
                        'breed-info-item';

                    if (field[2]) {
                        item.classList.add('wide');
                    }

                    const label =
                        document.createElement('div');

                    label.className =
                        'breed-info-label';

                    label.textContent = field[0];

                    const value =
                        document.createElement('div');

                    value.className =
                        'breed-info-value';

                    value.textContent = field[1];

                    item.appendChild(label);
                    item.appendChild(value);
                    grid.appendChild(item);
                });

                sectionEl.appendChild(heading);
                sectionEl.appendChild(grid);
                sections.appendChild(sectionEl);
            });

            const descriptionSection =
                document.createElement('section');

            descriptionSection.className =
                'breed-info-section';

            const descriptionHeading =
                document.createElement('h3');

            descriptionHeading.className =
                'breed-info-section-title';

            descriptionHeading.textContent =
                'Breed Description';

            const description =
                document.createElement('p');

            description.className =
                'breed-info-description';

            description.textContent =
                info.description;

            const extraDescription =
                document.createElement('p');

            extraDescription.className =
                'breed-info-description extra';

            extraDescription.textContent =
                info.extendedDescription;

            descriptionSection.appendChild(
                descriptionHeading
            );

            descriptionSection.appendChild(
                description
            );

            descriptionSection.appendChild(
                extraDescription
            );

            sections.appendChild(
                descriptionSection
            );
        }

        const identification =
            document.getElementById(
                'breedIdentification'
            );

        if (identification) {
            identification.innerHTML = '';

            info.identification.forEach(text => {
                const item =
                    document.createElement('li');

                item.textContent = text;
                identification.appendChild(item);
            });
        }

        const careChecklist =
            document.getElementById(
                'breedCareChecklist'
            );

        if (careChecklist) {
            careChecklist.innerHTML = '';

            info.careChecklist.forEach(text => {
                const item =
                    document.createElement('li');

                item.textContent = text;
                careChecklist.appendChild(item);
            });
        }

        const comparisonNote =
            document.getElementById(
                'breedComparisonNote'
            );

        if (comparisonNote) {
            comparisonNote.textContent =
                info.comparisonNote;
        }

        const modelNote =
            document.getElementById(
                'breedModelNote'
            );

        if (modelNote) {
            modelNote.textContent =
                info.modelNote;
        }

        modal.classList.add('show');
        modal.style.display = 'flex';
        modal.style.pointerEvents = 'auto';
        modal.setAttribute(
            'aria-hidden',
            'false'
        );
    }

    function closeBreedInfoModal() {
        const modal =
            document.getElementById('breedInfoModal');

        if (!modal) {
            return;
        }

        modal.classList.remove('show');
        modal.style.display = 'none';
        modal.style.pointerEvents = 'none';
        modal.setAttribute(
            'aria-hidden',
            'true'
        );

        const image =
            document.getElementById(
                'breed-modal-image'
            );

        if (image) {
            image.removeAttribute('src');
        }

        const confidenceFill =
            document.getElementById(
                'breed-confidence-fill'
            );

        if (confidenceFill) {
            confidenceFill.style.width = '0%';
        }

        /*
         * Camera-driven detection can continue once the detailed
         * information is closed.
         */
        if (stream) {
            startAutoDetection();
        }
    }

    function scanAgain() {
        closeBreedInfoModal();
        clearResults();

        lastClassification = null;
        modalCooldownUntil = Date.now() + 1000;

        /*
         * Offer the file picker when no camera is active; otherwise the
         * running camera can capture the next quail immediately.
         */
        if (!stream) {
            const uploadInput =
                document.getElementById(
                    'image-upload'
                );

            if (uploadInput) {
                uploadInput.value = '';
                uploadInput.click();
            }
        }
    }

    function makeThumbnail(
        imageDataUrl,
        maxWidth = 160,
        quality = 0.5
    ) {
        return new Promise(resolve => {
            const image = new Image();

            image.onload = function() {
                const scale = Math.min(
                    1,
                    maxWidth / image.width
                );

                const thumbCanvas =
                    document.createElement('canvas');

                thumbCanvas.width = Math.max(
                    1,
                    Math.round(image.width * scale)
                );

                thumbCanvas.height = Math.max(
                    1,
                    Math.round(image.height * scale)
                );

                const context =
                    thumbCanvas.getContext('2d');

                context.drawImage(
                    image,
                    0,
                    0,
                    thumbCanvas.width,
                    thumbCanvas.height
                );

                resolve(
                    thumbCanvas.toDataURL(
                        'image/jpeg',
                        quality
                    )
                );
            };

            image.onerror = function() {
                resolve(imageDataUrl);
            };

            image.src = imageDataUrl;
        });
    }

    async function classifyUploadedImage(file) {
        if (!file || !file.type.startsWith('image/')) {
            alert('Please choose an image file.');
            return;
        }

        const reader = new FileReader();

        reader.onload = async function(event) {
            const originalData =
                String(event.target.result);

            const imageData =
                await makeThumbnail(
                    originalData,
                    480,
                    0.72
                );

            stopAutoDetection();
            lastClassification = null;
            classificationInProgress = true;

            const detectedAt =
                new Date().toISOString();

            const processingRecord =
                createDetectionRecord({
                    detectionType: 'quail',
                    objectClass: 'uploaded quail image',
                    objectConfidence: null,
                    imageData: imageData,
                    detectedAt: detectedAt,
                    source: 'upload',
                    processing: true
                });

            openDetectionResult(
                processingRecord,
                { save: false }
            );

            /*
             * Capture the token AFTER opening the processing modal because
             * openDetectionResult() increments detectionDialogToken.
             * Otherwise the final result would never replace the modal.
             */
            const dialogToken =
                detectionDialogToken;

            modalCooldownUntil =
                Date.now() + 10000;

            try {
                const classificationResult =
                    await classifyBreedFromImage(
                        imageData
                    );

                const finalRecord =
                    createDetectionRecord({
                        detectionType: 'quail',
                        objectClass:
                            'uploaded quail image',
                        objectConfidence: null,
                        classification:
                            classificationResult,
                        imageData: imageData,
                        detectedAt: detectedAt,
                        source: 'upload'
                    });

                if (
                    dialogToken ===
                    detectionDialogToken
                ) {
                    openDetectionResult(
                        finalRecord,
                        { save: true }
                    );
                } else {
                    persistDetectionResult(
                        finalRecord
                    );
                }

            } catch (error) {
                console.error(
                    'Upload classification failed:',
                    error
                );

                const fallbackRecord =
                    createDetectionRecord({
                        detectionType: 'quail',
                        objectClass:
                            'uploaded quail image',
                        objectConfidence: null,
                        classification: {
                            success: false
                        },
                        imageData: imageData,
                        detectedAt: detectedAt,
                        source: 'upload'
                    });

                if (
                    dialogToken ===
                    detectionDialogToken
                ) {
                    openDetectionResult(
                        fallbackRecord,
                        { save: true }
                    );
                } else {
                    persistDetectionResult(
                        fallbackRecord
                    );
                }

            } finally {
                classificationInProgress =
                    false;

                lastDetectionCompletedAt =
                    Date.now();

                modalCooldownUntil =
                    lastDetectionCompletedAt +
                    DETECTION_COOLDOWN_MS;
            }
        };

        reader.onerror = function() {
            alert('Unable to read the selected image.');
        };

        reader.readAsDataURL(file);
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
    function renderDetectionResultModal(record) {
        const result = record && record.detectionType
            ? record
            : normalizeStoredDetection(record);

        const isQuail =
            result.detectionType === 'quail';
        const status = result.classificationStatus;

        const statusMap = {
            processing: {
                className: 'processing',
                title: 'Processing Detection',
                message: 'Identifying the breed from the captured image.'
            },
            identified: {
                className: 'success',
                title: 'Quail Detected!',
                message: 'Breed identified from the captured image.'
            },
            uncertain: {
                className: 'uncertain',
                title: 'Bird Detected',
                message: 'The breed could not be confirmed from this image.'
            },
            unavailable: {
                className: 'uncertain',
                title: 'Bird Detected',
                message: 'No breed was identified from this image.'
            },
            legacy: {
                className: '',
                title: 'Saved Quail Detection',
                message: result.classificationMessage
            },
            not_applicable: {
                className: '',
                title:
                    unavailableValue(
                        result.objectClass,
                        'Object'
                    ) + ' Detected',
                message: result.classificationMessage
            }
        };

        const statusView = statusMap[status] ||
            statusMap.not_applicable;

        const recordedBreedName =
            resolveRecordedBreedName(result);

        const identifiedBreed =
            status === 'processing'
                ? 'Processing…'
                : status === 'identified'
                    ? (recordedBreedName || 'Breed Not Identified')
                    : (isQuail
                        ? 'Breed Not Identified'
                        : 'Not applicable');

        const breedConfidenceDisplay =
            finiteNumber(result.breedConfidence) !== null
                ? Math.round(
                    Math.min(
                        100,
                        Math.max(
                            0,
                            finiteNumber(result.breedConfidence) * 100
                        )
                    )
                ) + '%'
                : '100%';

        const breedConfidenceHtml =
            status === 'identified' &&
            identifiedBreed !== 'Breed Not Identified'
                ? `
                    <div class="result-confidence-summary">
                        <span>Color Match</span>
                        <strong>${breedConfidenceDisplay}</strong>
                    </div>
                `
                : '';

        const imageHtml = result.imageData
            ? `<img src="${escapeHtml(result.imageData)}" alt="Captured detection result">`
            : `<div class="result-image-empty">No image recorded</div>`;

        const resultImage = document.getElementById(
            'detectionResultImage'
        );

        if (resultImage) {
            resultImage.innerHTML = imageHtml;
        }

        const reference = resolveBreedReference(result);
        const guide =
            isQuail &&
            reference &&
            reference.reference_key &&
            LEARNBOOK_GUIDES[reference.reference_key]
                ? LEARNBOOK_GUIDES[reference.reference_key]
                : null;

        let breedGuideHtml = '';

        if (guide) {
            const factsHtml = guide.facts
                .map(([label, value]) => `
                    <div class="result-fact">
                        <span class="result-fact-label">${escapeHtml(label)}</span>
                        <span class="result-fact-value">${escapeHtml(value)}</span>
                    </div>
                `)
                .join('');

            const careHtml = guide.care
                .map(section => `
                    <div class="result-guide-section">
                        <h4 class="result-guide-subtitle">${escapeHtml(section.title)}</h4>
                        <ul class="result-guide-list">
                            ${section.items
                                .map(item => `<li>${escapeHtml(item)}</li>`)
                                .join('')}
                        </ul>
                    </div>
                `)
                .join('');

            breedGuideHtml = `
                <section class="result-section result-guide">
                    <h3 class="result-section-title result-section-title-upper">
                        <svg class="icon" width="18" height="18" aria-hidden="true">
                            <use xlink:href="#icon-quail"/>
                        </svg>
                        ${escapeHtml(guide.title)}
                    </h3>
                    <p class="result-description">${escapeHtml(guide.overview)}</p>
                    <div class="result-facts">${factsHtml}</div>
                    <div class="result-guide-care">${careHtml}</div>
                </section>
            `;
        }

        const html = `
            <div class="result-details">
                <div>
                    <div class="modal-status-text" id="detection-result-heading">${escapeHtml(statusView.title)}</div>
                    <div class="modal-status-details">${escapeHtml(statusView.message)}</div>
                </div>

                <div class="result-breed-line">
                    <span class="result-breed-line-label">Breed:</span>
                    <span class="result-breed-line-value${identifiedBreed === 'Breed Not Identified' || identifiedBreed === 'Processing…' ? ' muted' : ''}">${escapeHtml(identifiedBreed)}</span>
                </div>

                ${breedConfidenceHtml}

                <div class="result-facts result-mini-facts">
                    <div class="result-fact">
                        <span class="result-fact-label">Detection Date &amp; Time</span>
                        <span class="result-fact-value">${escapeHtml(formatDateTime(result.detectedAt))}</span>
                    </div>
                </div>

                ${breedGuideHtml}
            </div>
        `;

        const content = document.getElementById(
            'detectionResultContent'
        );

        if (content) {
            content.innerHTML = html;
        }
    }

    function openDetectionResult(record, options = {}) {
        const result = record && record.detectionType
            ? record
            : normalizeStoredDetection(record);

        renderDetectionResultModal(result);

        const modal = document.getElementById(
            'detectionModal'
        );

        if (!modal) {
            return result;
        }

        detectionDialogToken += 1;

        modal.classList.add('show');
        modal.style.display = 'flex';
        modal.style.pointerEvents = 'auto';
        modal.style.zIndex = '99999';
        modal.setAttribute('aria-hidden', 'false');

        const closeHandler = event => {
            if (event) {
                event.preventDefault();
                event.stopPropagation();
            }

            closeDetectionModalAndRefresh(false);
        };

        const closeTop = document.getElementById(
            'detectionModalCloseButton'
        );

        const closeBottom = document.getElementById(
            'detectionModalCloseBottomButton'
        );

        const scanAgain = document.getElementById(
            'detectionModalScanAgainButton'
        );

        if (closeTop) {
            closeTop.disabled = false;
            closeTop.onclick = closeHandler;
        }

        if (closeBottom) {
            closeBottom.disabled = false;
            closeBottom.onclick = closeHandler;
        }

        if (scanAgain) {
            scanAgain.disabled = false;
            scanAgain.onclick = event => {
                closeHandler(event);
                startAutoDetection();
            };
        }

        if (
            typeof updateModalOverlayButtonState === 'function'
        ) {
            updateModalOverlayButtonState();
        }

        if (
            result.classificationStatus === 'identified' &&
            result.breedReference &&
            result.breedReference.breed_id
        ) {
            setCurrentBreedFromDetection(
                result.breedReference.breed_id
            );
        }

        if (options.save === true) {
            persistDetectionResult(result);
        }

        lastDetectionRecord = result;

        return result;
    }

    function persistDetectionResult(record) {
        if (!record || record.saved) {
            return;
        }

        record.saved = true;
        addDetectionToHistory(record);
        postDetectionResult(record);
    }

    function postDetectionResult(record) {
        const allowedTypes = [
            'quail',
            'human',
            'object',
            'unknown'
        ];

        const detectionType =
            allowedTypes.indexOf(record.detectionType) !== -1
                ? record.detectionType
                : 'unknown';

        const identified =
            record.classificationStatus === 'identified' &&
            record.breedName;

        const payload = {
            client_detection_id: String(record.id),
            detected_at: record.detectedAt,
            detection_type: detectionType,
            quail_breed_id: identified &&
                record.breedReference &&
                record.breedReference.breed_id
                ? record.breedReference.breed_id
                : null,
            confidence: finiteNumber(record.objectConfidence),
            object_class: record.objectDetectionResult,
            object_detection_result:
                record.objectDetectionResult,
            object_confidence:
                finiteNumber(record.objectConfidence),
            classification_status:
                record.classificationStatus,
            breed_name: identified ? record.breedName : null,
            model_class_name: record.modelClassName,
            breed_reference_key:
                record.breedReferenceKey,
            breed_confidence:
                finiteNumber(record.breedConfidence),
            breed_reference:
                identified ? record.breedReference : null,
            temperature: finiteNumber(record.temperature),
            humidity: finiteNumber(record.humidity),
            sensor_source: record.sensorSource,
            sensor_status: record.sensorStatus,
            recorded_at: record.sensorRecordedAt,
            location:
                record.source === 'upload'
                    ? 'Uploaded Image'
                    : 'Camera Feed',
            detection_notes:
                record.classificationMessage || '',
            image_data: record.imageData
        };

        fetch(
            '{{ route("quail-detection.save") }}',
            {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN':
                        document.querySelector(
                            'meta[name="csrf-token"]'
                        )?.content || ''
                },
                body: JSON.stringify(payload)
            }
        )
            .then(response => {
                if (!response.ok) {
                    throw new Error(
                        'Save failed with status ' +
                        response.status
                    );
                }

                return response.json();
            })
            .then(data => {
                if (data && data.success) {
                    console.log(
                        'Detection saved with recorded result:',
                        data.detection_id
                    );
                }
            })
            .catch(error => {
                console.warn(
                    'Detection history save failed:',
                    error
                );
            });
    }

    /*
 * FIXED CLOSE FUNCTION
 */
function closeDetectionModalAndRefresh(refresh = false) {
    const modal = document.getElementById('detectionModal');

    detectionDialogToken += 1;

    if (!modal) {
        return;
    }

    // Keep auto detection running so the next detected object
    // can trigger a new modal without refreshing the page.
    // Hide modal
    modal.classList.remove('show');
    modal.setAttribute('aria-hidden', 'true');
    modal.style.display = 'none';
    modal.style.pointerEvents = 'none';

    // Prevent consecutive frames of the same bird from reopening
    // the dialog immediately.
    modalCooldownUntil =
        Date.now() + DETECTION_COOLDOWN_MS;

    // IMPORTANT: closing the modal must NOT stop a live camera session.
    if (stream) {
        startAutoDetection();
    }

    if (refresh) {
        console.log('Modal closed with refresh requested');
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

            /*
             * Preload the detector while the page is idle so Start Camera
             * does not have to wait for the model to download.
             */
            ensureCocoModel()
                .catch(error =>
                    console.warn(
                        'Detector preload failed:',
                        error
                    )
                );

            const uploadInput =
                document.getElementById(
                    'image-upload'
                );

            if (uploadInput) {
                uploadInput.addEventListener(
                    'change',
                    function(event) {
                        const file =
                            event.target.files &&
                            event.target.files[0];

                        if (file) {
                            classifyUploadedImage(
                                file
                            );
                        }
                    }
                );
            }

            const breedModal =
                document.getElementById(
                    'breedInfoModal'
                );

            const breedCloseTopButton =
                document.getElementById(
                    'breedModalCloseTopButton'
                );

            const breedCloseButton =
                document.getElementById(
                    'breedModalCloseButton'
                );

            const scanAgainButton =
                document.getElementById(
                    'breedModalScanAgainButton'
                );

            if (breedCloseTopButton) {
                breedCloseTopButton.addEventListener(
                    'click',
                    function(event) {
                        event.preventDefault();
                        event.stopPropagation();
                        closeBreedInfoModal();
                    }
                );
            }

            if (breedCloseButton) {
                breedCloseButton.addEventListener(
                    'click',
                    function(event) {
                        event.preventDefault();
                        event.stopPropagation();
                        closeBreedInfoModal();
                    }
                );
            }

            if (scanAgainButton) {
                scanAgainButton.addEventListener(
                    'click',
                    function(event) {
                        event.preventDefault();
                        event.stopPropagation();
                        scanAgain();
                    }
                );
            }

            if (breedModal) {
                breedModal.addEventListener(
                    'click',
                    function(event) {
                        if (
                            event.target ===
                            breedModal
                        ) {
                            closeBreedInfoModal();
                        }
                    }
                );

                const breedModalContent =
                    breedModal.querySelector(
                        '.modal-content'
                    );

                if (breedModalContent) {
                    breedModalContent.addEventListener(
                        'click',
                        function(event) {
                            event.stopPropagation();
                        }
                    );
                }
            }

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
                            false
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
                                false
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

                        const breedModalEl =
                            document.getElementById(
                                'breedInfoModal'
                            );

                        if (
                            breedModalEl &&
                            breedModalEl.classList.contains(
                                'show'
                            )
                        ) {
                            closeBreedInfoModal();
                            return;
                        }

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