<!-- DAILY SUMMARY Category -->
<div id="summary" class="category-content">
    <!-- Revenue Overview -->
    <div class="section-card">
        <h2 class="section-title">💰 Revenue Overview</h2>
        <div class="info-grid">
            <div class="info-card" style="background: linear-gradient(135deg, #4caf50, #2e7d32); color: white;">
                <div class="info-card-icon" style="color: white;">💰</div>
                <div class="info-card-title" style="color: rgba(255,255,255,0.9);">Total Revenue</div>
                <div class="info-card-text" id="summaryTotalIncome" style="color: white; font-size: 1.8rem; font-weight: bold;">₱0.00</div>
            </div>
            <div class="info-card">
                <div class="info-card-icon">🥚</div>
                <div class="info-card-title">Eggs Revenue</div>
                <div class="info-card-text" id="summaryEggsIncome">₱0.00</div>
            </div>
            <div class="info-card">
                <div class="info-card-icon"></div>
                <div class="info-card-title">Quail Revenue</div>
                <div class="info-card-text" id="summaryQuailIncome">₱0.00</div>
            </div>
        </div>
    </div>

    <!-- Production Summary -->
    <div class="section-card">
        <h2 class="section-title">📊 Production Summary</h2>
        <div class="info-grid">
            <div class="info-card">
                <div class="info-card-icon">🥚</div>
                <div class="info-card-title">Total Collected</div>
                <div class="info-card-text" id="summaryTotalCollected">0 eggs</div>
            </div>
            <div class="info-card">
                <div class="info-card-icon">✅</div>
                <div class="info-card-title">Good Eggs</div>
                <div class="info-card-text" id="summaryGoodEggs">0 eggs</div>
            </div>
            <div class="info-card">
                <div class="info-card-icon">⚠️</div>
                <div class="info-card-title">Damaged Eggs</div>
                <div class="info-card-text" id="summaryCrackedEggs">0 eggs</div>
            </div>
            <div class="info-card">
                <div class="info-card-icon">📦</div>
                <div class="info-card-title">Remaining Stock</div>
                <div class="info-card-text" id="summaryRemainingStock">0 eggs</div>
            </div>
        </div>
    </div>

    <!-- Sales Summary -->
    <div class="section-card">
        <h2 class="section-title">🛒 Sales Summary</h2>
        <div class="info-grid">
            <div class="info-card">
                <div class="info-card-icon">🥚</div>
                <div class="info-card-title">Eggs Sold</div>
                <div class="info-card-text" id="summaryEggsSold">0 trays</div>
            </div>
            <div class="info-card">
                <div class="info-card-icon"></div>
                <div class="info-card-title">Live Quail Sold</div>
                <div class="info-card-text" id="summaryLiveQuail">0 pcs</div>
            </div>
            <div class="info-card">
                <div class="info-card-icon">🍗</div>
                <div class="info-card-title">Dressed Quail Sold</div>
                <div class="info-card-text" id="summaryDressedQuail">0 pcs</div>
            </div>
        </div>
    </div>

    <!-- Activity Log -->
    <div class="section-card">
        <h2 class="section-title">📋 Today's Activities</h2>
        <div id="summaryActivityLog" style="background: #faf7f5; border-radius: 12px; padding: 1rem; max-height: 300px; overflow-y: auto;">
            <div class="activity-empty" style="color: #a1887f; text-align: center; padding: 1rem;">No activities recorded yet today.</div>
        </div>
    </div>

    <!-- Export Options -->
    <div class="section-card">
        <h2 class="section-title">📥 Export Report</h2>
        <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
            <button onclick="printSummary()" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 0.5rem;">
                🖨️ Print Summary
            </button>
            <button onclick="downloadSummary()" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 0.5rem;">
                📄 Download CSV
            </button>
        </div>
    </div>
</div>
