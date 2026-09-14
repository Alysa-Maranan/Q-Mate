@extends('layouts.app')

@section('content')
<style>
    body {
        background: linear-gradient(135deg, #f5f0eb 0%, #efe8e4 50%, #e8dcd6 100%);
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    }

    .sidebar-content-wrap {
        margin-left: 260px;
        transition: margin-left 0.3s ease;
        padding-top: 0;
    }

    .container-feed {
        padding: 2rem;
    }

    .header-feed {
        background: white;
        border-radius: 24px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 8px 32px rgba(161, 136, 127, 0.12);
        border: 1px solid rgba(161, 136, 127, 0.1);
        text-align: center;
    }

    .header-feed h1 {
        font-size: 2rem;
        font-weight: 800;
        color: #6d4c41;
        margin: 0;
    }

    .header-feed p {
        margin: 1rem 0 0 0;
        color: #8d6e63;
        font-size: 1rem;
    }

    .feed-detection-wrapper {
        background: white;
        border-radius: 20px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 8px 32px rgba(161, 136, 127, 0.12);
        border: 1px solid rgba(161, 136, 127, 0.1);
    }

    .camera-feed-wrapper {
        position: relative;
        width: 100%;
        max-width: 500px;
        margin: 0 auto;
        border-radius: 16px;
        overflow: hidden;
        background: #000;
        aspect-ratio: 4/3;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    #feed-video {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .button-group {
        display: flex;
        gap: 1rem;
        justify-content: center;
        margin-top: 1.5rem;
        flex-wrap: wrap;
    }

    .btn {
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 10px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 0.95rem;
    }

    .btn-primary {
        background: linear-gradient(135deg, #6d4c41 0%, #a1887f 100%);
        color: white;
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

    .compatibility-result {
        margin-top: 2rem;
        padding: 1.5rem;
        border-radius: 16px;
        border: 2px solid #d7ccc8;
        display: none;
    }

    .compatibility-result.show {
        display: block;
    }

    .compatibility-result.suitable {
        background: #e8f5e9;
        border-color: #4caf50;
    }

    .compatibility-result.caution {
        background: #fff3e0;
        border-color: #ff9800;
    }

    .compatibility-result.unsuitable {
        background: #ffebee;
        border-color: #f44336;
    }

    .feed-info-panel {
        background: #efebe9;
        border-radius: 12px;
        padding: 1.5rem;
        margin-top: 1.5rem;
        border-left: 4px solid #a1887f;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
    }

    .info-item {
        background: white;
        padding: 1rem;
        border-radius: 8px;
        border: 1px solid #d7ccc8;
    }

    .info-label {
        font-size: 0.85rem;
        font-weight: 600;
        color: #8d6e63;
        text-transform: uppercase;
        margin-bottom: 0.5rem;
    }

    .info-value {
        font-size: 1rem;
        font-weight: 700;
        color: #6d4c41;
    }

    .recent-detections {
        margin-top: 2rem;
        background: #f5f0eb;
        border-radius: 12px;
        padding: 1.5rem;
    }

    .detection-item {
        background: white;
        padding: 1rem;
        border-radius: 8px;
        margin-bottom: 0.5rem;
        border-left: 3px solid #a1887f;
        font-size: 0.9rem;
    }

    @media (max-width: 768px) {
        .container-feed {
            padding: 1rem;
        }

        .button-group {
            flex-direction: column;
        }

        .btn {
            width: 100%;
        }
    }
</style>

@include('components.sidebar')

<div id="main-content-wrap" class="sidebar-content-wrap">
    <div class="container-feed">
        <!-- Header -->
        <div class="header-feed">
            <h1>🍽️ Feed Compatibility Check</h1>
            <p>Scan food items to verify they're suitable for your detected quail breed</p>
        </div>

        <!-- Main Detection Section -->
        <div class="feed-detection-wrapper">
            <h2 style="color: #6d4c41; margin: 0 0 1.5rem 0; font-size: 1.2rem; font-weight: 700;">Feed Camera</h2>
            
            <div class="camera-feed-wrapper">
                <video id="feed-video" autoplay playsinline style="display: none;"></video>
                <div id="feed-placeholder" style="color: #ccc; text-align: center;">
                    <div style="font-size: 3rem; margin-bottom: 1rem;">🍗</div>
                    <div>Initializing camera...</div>
                </div>
            </div>

            <!-- Breed & Feed Selection -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-top: 1.5rem;">
                <div>
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #6d4c41;">Select Quail Breed</label>
                    <select id="breed-select" style="width: 100%; padding: 0.75rem; border: 2px solid #d7ccc8; border-radius: 8px; font-size: 0.95rem;">
                        <option value="">-- Choose Breed --</option>
                        @foreach($quailBreeds as $breed)
                            <option value="{{ $breed->id }}">{{ $breed->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #6d4c41;">Select Feed Item</label>
                    <select id="feed-select" style="width: 100%; padding: 0.75rem; border: 2px solid #d7ccc8; border-radius: 8px; font-size: 0.95rem;">
                        <option value="">-- Choose Feed --</option>
                        @foreach($feedItems as $feed)
                            <option value="{{ $feed->id }}">{{ $feed->name }} ({{ $feed->category }})</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Controls -->
            <div class="button-group">
                <button class="btn btn-primary" id="start-feed-btn" onclick="startFeedCamera()">
                    ▶️ Start Camera
                </button>
                <button class="btn btn-primary" id="check-btn" onclick="checkFeedCompatibility()" disabled>
                    🔍 Check Compatibility
                </button>
                <button class="btn btn-secondary" id="stop-feed-btn" onclick="stopFeedCamera()" style="display: none;">
                    ⏹️ Stop Camera
                </button>
                <button class="btn btn-secondary" onclick="clearFeedResults()">
                    🔄 Clear
                </button>
            </div>

            <!-- Compatibility Result -->
            <div id="compatibility-result" class="compatibility-result">
                <div style="text-align: center; margin-bottom: 1rem;">
                    <div style="font-size: 2rem; margin-bottom: 0.5rem;">
                        <span id="result-icon">❓</span>
                    </div>
                    <div style="font-size: 1.25rem; font-weight: 700; margin-bottom: 0.3rem;">
                        <span id="result-status">Analyzing...</span>
                    </div>
                    <div style="font-size: 0.95rem; color: #666;">
                        <span id="result-message"></span>
                    </div>
                </div>
            </div>

            <!-- Feed Info Panel -->
            <div id="feed-info" class="feed-info-panel" style="display: none;">
                <h3 style="margin: 0 0 1rem 0; color: #6d4c41;">Feed Details</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Protein</div>
                        <div class="info-value" id="feed-protein">N/A</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Fat</div>
                        <div class="info-value" id="feed-fat">N/A</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Fiber</div>
                        <div class="info-value" id="feed-fiber">N/A</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Category</div>
                        <div class="info-value" id="feed-category">N/A</div>
                    </div>
                </div>
                <div style="margin-top: 1rem; padding: 1rem; background: white; border-radius: 8px;">
                    <div style="font-weight: 600; color: #6d4c41; margin-bottom: 0.5rem;">Benefits</div>
                    <div style="font-size: 0.9rem; color: #666;" id="feed-benefits"></div>
                </div>
            </div>

            <!-- Recent Feed Detections -->
            @if($recentFeedDetections->count() > 0)
            <div class="recent-detections">
                <h3 style="margin: 0 0 1rem 0; color: #6d4c41;">Recent Feed Checks</h3>
                @foreach($recentFeedDetections->take(5) as $detection)
                    <div class="detection-item">
                        <strong>{{ $detection->feed_name ?? ($detection->feedItem?->name ?? 'Unknown Feed') }}</strong>
                        <span style="float: right; font-weight: 600;">
                            @if($detection->compatibility_status === 'suitable')
                                ✅ Suitable
                            @elseif($detection->compatibility_status === 'unsuitable')
                                ❌ Not Suitable
                            @else
                                ⚠️ Caution
                            @endif
                        </span>
                        <br>
                        <small style="color: #999;">{{ $detection->breed?->name ?? 'Unknown Breed' }} • {{ $detection->created_at->diffForHumans() }}</small>
                    </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>
</div>

<script>
    let feedVideo = null;
    let feedStream = null;
    let feedCameraReady = false;

    async function startFeedCamera() {
        feedVideo = document.getElementById('feed-video');
        
        try {
            feedStream = await navigator.mediaDevices.getUserMedia({
                video: {
                    facingMode: 'environment',
                    width: { ideal: 640 },
                    height: { ideal: 480 }
                }
            });
            
            feedVideo.srcObject = feedStream;
            feedVideo.style.display = 'block';
            document.getElementById('feed-placeholder').style.display = 'none';
            document.getElementById('check-btn').disabled = false;
            document.getElementById('start-feed-btn').style.display = 'none';
            document.getElementById('stop-feed-btn').style.display = 'inline-block';
            feedCameraReady = true;
            
        } catch (err) {
            alert('Camera access denied: ' + err.message);
        }
    }

    function stopFeedCamera() {
        if (feedStream) {
            feedStream.getTracks().forEach(track => track.stop());
        }
        feedVideo.style.display = 'none';
        document.getElementById('feed-placeholder').style.display = 'flex';
        document.getElementById('check-btn').disabled = true;
        document.getElementById('start-feed-btn').style.display = 'inline-block';
        document.getElementById('stop-feed-btn').style.display = 'none';
        feedCameraReady = false;
    }

    async function checkFeedCompatibility() {
        const breedId = document.getElementById('breed-select').value;
        const feedId = document.getElementById('feed-select').value;

        if (!breedId) {
            alert('Please select a quail breed');
            return;
        }

        if (!feedId) {
            alert('Please select a feed item');
            return;
        }

        // Simulate feed compatibility check
        const resultDiv = document.getElementById('compatibility-result');
        resultDiv.classList.add('show');

        // Demo logic - in production, use actual ML
        const compatibility = Math.random() > 0.4 ? 'suitable' : (Math.random() > 0.3 ? 'caution' : 'unsuitable');
        
        resultDiv.className = 'compatibility-result show ' + compatibility;

        if (compatibility === 'suitable') {
            document.getElementById('result-icon').textContent = '✅';
            document.getElementById('result-status').textContent = 'Feed Suitable';
            document.getElementById('result-message').textContent = 'This feed is recommended for the selected breed';
        } else if (compatibility === 'caution') {
            document.getElementById('result-icon').textContent = '⚠️';
            document.getElementById('result-status').textContent = 'Use With Caution';
            document.getElementById('result-message').textContent = 'Feed can be given but monitor closely';
        } else {
            document.getElementById('result-icon').textContent = '❌';
            document.getElementById('result-status').textContent = 'Not Recommended';
            document.getElementById('result-message').textContent = 'This feed is not suitable for this breed';
        }

        // Show feed details
        showFeedDetails(feedId);

        // Save detection
        saveFeedDetection(breedId, feedId, compatibility);
    }

    function showFeedDetails(feedId) {
        const feeds = {!! json_encode($feedItems->keyBy('id')) !!};
        const feed = feeds[feedId];

        if (feed) {
            document.getElementById('feed-info').style.display = 'block';
            document.getElementById('feed-protein').textContent = (feed.protein_percentage || 'N/A') + '%';
            document.getElementById('feed-fat').textContent = (feed.fat_percentage || 'N/A') + '%';
            document.getElementById('feed-fiber').textContent = (feed.fiber_percentage || 'N/A') + '%';
            document.getElementById('feed-category').textContent = feed.category || 'N/A';
            document.getElementById('feed-benefits').textContent = feed.health_benefits || 'No specific benefits listed';
        }
    }

    function saveFeedDetection(breedId, feedId, compatibility) {
        fetch('{{ route("feed-detection.save") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                quail_breed_id: breedId,
                feed_item_id: feedId,
                compatibility_status: compatibility,
                confidence: 0.85
            })
        }).catch(err => console.log('Save optional:', err));
    }

    function clearFeedResults() {
        document.getElementById('compatibility-result').classList.remove('show');
        document.getElementById('feed-info').style.display = 'none';
        document.getElementById('breed-select').value = '';
        document.getElementById('feed-select').value = '';
    }

    // Cleanup on page exit
    window.addEventListener('beforeunload', function() {
        if (feedStream) {
            feedStream.getTracks().forEach(track => track.stop());
        }
    });
</script>
@endsection
