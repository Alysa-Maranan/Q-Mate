<!-- DAILY SUMMARY Category -->
<div id="summary" class="category-content">
    <!-- Search & Filter Section -->
    <div class="section-card">
        <h2 class="section-title">🔍 Search & Filter</h2>
        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">Search by Date</label>
                <input type="date" class="form-input" id="filterDate" onchange="filterSummaryData()">
            </div>
            <div class="form-group">
                <label class="form-label">Filter by Type</label>
                <select class="form-input" id="filterType" onchange="filterSummaryData()">
                    <option value="">All Types</option>
                    <option value="collection">Egg Collection</option>
                    <option value="production">Production Record</option>
                    <option value="inspection">Inspection</option>
                    <option value="stock">Stock Update</option>
                    <option value="sale">Sale</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Search Activity</label>
                <input type="text" class="form-input" id="searchActivity" placeholder="Search..." onkeyup="filterSummaryData()">
            </div>
        </div>
    </div>

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

    <!-- Daily Summary Table -->
    <div class="section-card">
        <h2 class="section-title">📊 Daily Summary Records</h2>
        <div style="overflow-x: auto;">
            <table class="table" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: linear-gradient(135deg, #a1887f 0%, #6d4c41 100%);">
                        <th style="padding: 1rem; text-align: left; color: #fff; font-weight: 700; font-size: 0.85rem;">Date</th>
                        <th style="padding: 1rem; text-align: left; color: #fff; font-weight: 700; font-size: 0.85rem;">Time</th>
                        <th style="padding: 1rem; text-align: left; color: #fff; font-weight: 700; font-size: 0.85rem;">Activity Type</th>
                        <th style="padding: 1rem; text-align: left; color: #fff; font-weight: 700; font-size: 0.85rem;">Details</th>
                        <th style="padding: 1rem; text-align: left; color: #fff; font-weight: 700; font-size: 0.85rem;">Quantity</th>
                        <th style="padding: 1rem; text-align: left; color: #fff; font-weight: 700; font-size: 0.85rem;">Amount</th>
                    </tr>
                </thead>
                <tbody id="summaryTableBody">
                    <tr>
                        <td colspan="6" style="padding: 2rem; text-align: center; color: #a1887f;">
                            No records yet. Start recording activities to see them here.
                        </td>
                    </tr>
                </tbody>
            </table>
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

    <!-- Export Options -->
    <div class="section-card">
        <h2 class="section-title">📥 Export Report</h2>
        <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
            <button onclick="printSummary()" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 0.5rem;">
                🖨️ Print Summary
            </button>
            <button onclick="downloadSummaryCSV()" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 0.5rem;">
                📄 Download CSV
            </button>
            <button onclick="downloadSummaryExcel()" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 0.5rem;">
                📊 Download Excel
            </button>
            <button onclick="downloadSummaryPDF()" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 0.5rem;">
                📕 Download PDF
            </button>
        </div>
    </div>
</div>

<script>
// Enhanced Daily Summary with Table Display
let allSummaryRecords = [];

function loadDailyDataToTable() {
    const today = new Date().toDateString();
    const saved = localStorage.getItem('dailyData_' + today);
    if (saved) {
        dailyData = JSON.parse(saved);
        allSummaryRecords = dailyData.activities.map(act => ({
            date: today,
            time: act.time,
            icon: act.icon,
            title: act.title,
            detail: act.detail,
            type: getActivityType(act.title)
        }));
        renderSummaryTable(allSummaryRecords);
        updateSummaryDisplay();
    }
}

function getActivityType(title) {
    if (title.includes('Collection')) return 'collection';
    if (title.includes('Production')) return 'production';
    if (title.includes('Inspection')) return 'inspection';
    if (title.includes('Stock')) return 'stock';
    if (title.includes('Sale')) return 'sale';
    return 'other';
}

function renderSummaryTable(records) {
    const tbody = document.getElementById('summaryTableBody');
    if (records.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" style="padding: 2rem; text-align: center; color: #a1887f;">No records found.</td></tr>';
        return;
    }
    
    tbody.innerHTML = records.map(record => `
        <tr style="border-bottom: 1px solid #efebe9;">
            <td style="padding: 1rem; font-size: 0.88rem; color: #6d4c41;">${new Date(record.date).toLocaleDateString()}</td>
            <td style="padding: 1rem; font-size: 0.88rem; color: #6d4c41;">${record.time}</td>
            <td style="padding: 1rem; font-size: 0.88rem; color: #4e342e;">
                <span style="display: inline-block; padding: 0.35rem 0.75rem; border-radius: 20px; font-size: 0.75rem; font-weight: 700; background: #efebe9; color: #6d4c41;">
                    ${record.icon} ${record.title}
                </span>
            </td>
            <td style="padding: 1rem; font-size: 0.88rem; color: #5d4037;">${record.detail}</td>
            <td style="padding: 1rem; font-size: 0.88rem; color: #4e342e;">-</td>
            <td style="padding: 1rem; font-size: 0.88rem; color: #4e342e;">-</td>
        </tr>
    `).join('');
}

function filterSummaryData() {
    const filterDate = document.getElementById('filterDate').value;
    const filterType = document.getElementById('filterType').value;
    const searchActivity = document.getElementById('searchActivity').value.toLowerCase();
    
    let filtered = allSummaryRecords;
    
    if (filterDate) {
        const selectedDate = new Date(filterDate).toDateString();
        filtered = filtered.filter(r => new Date(r.date).toDateString() === selectedDate);
    }
    
    if (filterType) {
        filtered = filtered.filter(r => r.type === filterType);
    }
    
    if (searchActivity) {
        filtered = filtered.filter(r => 
            r.title.toLowerCase().includes(searchActivity) || 
            r.detail.toLowerCase().includes(searchActivity)
        );
    }
    
    renderSummaryTable(filtered);
}

function downloadSummaryCSV() {
    let csvContent = "data:text/csv;charset=utf-8,";
    csvContent += "Date,Time,Activity Type,Details\n";
    
    allSummaryRecords.forEach(record => {
        csvContent += `"${new Date(record.date).toLocaleDateString()}","${record.time}","${record.title}","${record.detail.replace(/"/g, '""')}"\n`;
    });
    
    const encodedUri = encodeURI(csvContent);
    const link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", "daily_summary_" + new Date().toISOString().slice(0,10) + ".csv");
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

function downloadSummaryExcel() {
    let excelContent = '<table border="1"><tr style="background-color: #a1887f; color: white;"><th>Date</th><th>Time</th><th>Activity Type</th><th>Details</th><th>Total Revenue</th><th>Eggs Sold</th><th>Quail Sold</th></tr>';
    
    allSummaryRecords.forEach(record => {
        excelContent += `<tr><td>${new Date(record.date).toLocaleDateString()}</td><td>${record.time}</td><td>${record.title}</td><td>${record.detail}</td><td>-</td><td>-</td><td>-</td></tr>`;
    });
    
    excelContent += '</table>';
    
    const link = document.createElement("a");
    const blob = new Blob([excelContent], { type: 'application/vnd.ms-excel' });
    link.setAttribute("href", URL.createObjectURL(blob));
    link.setAttribute("download", "daily_summary_" + new Date().toISOString().slice(0,10) + ".xls");
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

function downloadSummaryPDF() {
    let pdfContent = '<html><head><title>Daily Summary Report</title>';
    pdfContent += '<style>body { font-family: Arial; padding: 20px; } table { width: 100%; border-collapse: collapse; } th, td { border: 1px solid #ddd; padding: 10px; text-align: left; } th { background-color: #a1887f; color: white; } h1 { color: #6d4c41; text-align: center; }</style>';
    pdfContent += '</head><body>';
    pdfContent += '<h1>Daily Summary Report</h1>';
    pdfContent += '<p style="text-align: center;">Date: ' + new Date().toLocaleDateString() + '</p>';
    pdfContent += '<table><tr><th>Date</th><th>Time</th><th>Activity Type</th><th>Details</th></tr>';
    
    allSummaryRecords.forEach(record => {
        pdfContent += `<tr><td>${new Date(record.date).toLocaleDateString()}</td><td>${record.time}</td><td>${record.title}</td><td>${record.detail}</td></tr>`;
    });
    
    pdfContent += '</table>';
    pdfContent += '<p style="margin-top: 30px; font-size: 12px; color: #666;">Generated on: ' + new Date().toLocaleString() + '</p>';
    pdfContent += '</body></html>';
    
    const printWindow = window.open('', '', 'height=600,width=800');
    printWindow.document.write(pdfContent);
    printWindow.document.close();
    printWindow.print();
}

// Update the original addActivity function to also update the table
const originalAddActivity = addActivity;
function addActivity(icon, title, detail) {
    originalAddActivity(icon, title, detail);
    loadDailyDataToTable();
}

// Load table on page load
document.addEventListener('DOMContentLoaded', function() {
    loadDailyDataToTable();
});
</script>
