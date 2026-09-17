@extends('layouts.app')

@section('content')
@include('components.sidebar')

<style>
    /* BOOK-STYLE LEARN BOOK - Professional Typography */
    * {
        box-sizing: border-box;
    }
    
    body {
        background: linear-gradient(135deg, #f5f0eb 0%, #efe8e4 50%, #e8dcd6 100%);
        min-height: 100vh;
        font-family: 'Instrument Sans', 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    }
    
    .book-wrapper {
        background: linear-gradient(135deg, #fcfbf9 0%, #fefcf9 100%);
        border-left: 8px solid #a1887f;
        border-right: 1px solid #e8dcd6;
        box-shadow: -10px 0 30px rgba(121, 85, 72, 0.1), 10px 0 30px rgba(0, 0, 0, 0.03);
        min-height: 100vh;
        position: relative;
        margin: 0;
    }
    
    .book-header {
        background: linear-gradient(135deg, #6d4c41 0%, #8d6e63 100%);
        color: #fefcf9;
        padding: 4rem 3rem 3rem;
        text-align: center;
        border-bottom: 4px solid #a1887f;
        position: relative;
        overflow: hidden;
    }
    
    .book-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        border-radius: 50%;
    }
    
    .book-title {
        font-size: 3rem;
        font-weight: 800;
        margin: 0 0 0.5rem;
        letter-spacing: -1px;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        position: relative;
        z-index: 1;
    }
    
    .book-subtitle {
        font-size: 1.25rem;
        font-weight: 400;
        letter-spacing: 2px;
        opacity: 0.95;
        position: relative;
        z-index: 1;
    }
    
    .book-content {
        max-width: 900px;
        margin: 0 auto;
        padding: 3rem 4rem;
        line-height: 1.9;
        width: 100%;
    }
    
    .sidebar-content-wrap.expanded .book-wrapper {
        max-width: 1600px;
        margin: 0 auto;
    }

    .sidebar-content-wrap.expanded .book-content {
        max-width: 95%;
        padding: 3rem 2rem;
    }
    
    @media (max-width: 1200px) {
        .book-content {
            max-width: 85%;
            padding: 3rem 2rem;
        }
    }
    
    @media (max-width: 768px) {
        .book-content {
            max-width: 100%;
            padding: 2rem 1.5rem;
        }
    }
    
    .book-toc {
        background: #efebe9;
        border: 2px solid #d7ccc8;
        border-radius: 12px;
        padding: 2rem;
        margin: 2rem 0;
    }
    
    .toc-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #6d4c41;
        margin-bottom: 1.5rem;
        text-align: center;
        border-bottom: 2px solid #a1887f;
        padding-bottom: 0.75rem;
    }
    
    .toc-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .toc-item {
        padding: 0.75rem 1rem;
        border-left: 4px solid transparent;
        transition: all 0.2s ease;
        cursor: pointer;
    }
    
    .toc-item:hover {
        background: #e0d7d0;
        border-left-color: #a1887f;
    }
    
    .toc-link {
        color: #6d4c41;
        text-decoration: none;
        font-weight: 600;
        display: block;
    }
    
    .toc-link:hover {
        color: #4e342e;
    }
    
    .chapter-marker {
        text-align: center;
        margin: 4rem 0 2rem;
        padding: 2rem 0;
        border-top: 2px solid #d7ccc8;
        border-bottom: 2px solid #d7ccc8;
    }
    
    .chapter-number {
        font-size: 0.9rem;
        letter-spacing: 2px;
        color: #a1887f;
        font-weight: 700;
        text-transform: uppercase;
    }
    
    .chapter-title {
        font-size: 2.5rem;
        font-weight: 800;
        color: #6d4c41;
        margin: 0.5rem 0;
        letter-spacing: -0.5px;
    }
    
    .chapter-subtitle {
        font-size: 1.1rem;
        color: #8d6e63;
        font-weight: 400;
        font-style: italic;
    }
    
    .notification-bell {
        position: relative;
        cursor: pointer;
        padding: 0.5rem;
        font-size: 1.5rem;
        background: white;
        border-radius: 50%;
        width: 45px;
        height: 45px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid #d7ccc8;
    }

    .notification-bell:hover {
        background: #efebe9;
    }

    .notification-badge {
        position: absolute;
        top: -5px;
        right: -5px;
        background: #ef4444;
        color: white;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        font-size: 0.75rem;
        display: none;
    }

    .notification-badge.has-notifications {
        display: flex;
        animation: pulse-badge 2s infinite;
    }

    @keyframes pulse-badge {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.2); }
    }

    .notification-dropdown {
        position: absolute;
        top: 60px;
        right: 0;
        width: 350px;
        max-height: 400px;
        overflow-y: auto;
        background: white;
        border-radius: 12px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
        border: 1px solid #d7ccc8;
        z-index: 1001;
        display: none;
    }

    .notification-dropdown.show {
        display: block;
        animation: slideDown 0.3s ease;
    }

    @keyframes slideDown {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .notification-header {
        padding: 1rem;
        border-bottom: 1px solid #d7ccc8;
        font-weight: 700;
        color: #6d4c41;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .notification-item {
        padding: 0.75rem 1rem;
        border-bottom: 1px solid #efebe9;
        display: flex;
        align-items: flex-start;
        gap: 0.5rem;
    }

    .notification-item:hover {
        background: #efebe9;
    }

    .notification-item:last-child {
        border-bottom: none;
    }

    .notification-item.success {
        border-left: 4px solid #a1887f;
    }

    .notification-item.info {
        border-left: 4px solid #8d6e63;
    }

    .notification-icon {
        font-size: 1.25rem;
    }

    .notification-content {
        flex: 1;
    }

    .notification-text {
        font-size: 0.875rem;
        color: #1f2937;
    }

    .notification-time {
        font-size: 0.75rem;
        color: #6b7280;
        margin-top: 0.25rem;
    }

    .notification-empty {
        padding: 2rem;
        text-align: center;
        color: #6b7280;
    }

    .clear-notifications {
        font-size: 0.75rem;
        color: #ef4444;
        cursor: pointer;
        text-decoration: underline;
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

    @media (max-width: 768px) {
        .sidebar-content-wrap {
            margin-left: 0 !important;
            padding-top: 70px;
        }
    }

    .section-card {
        background: transparent;
        border-radius: 0;
        padding: 2rem 0;
        box-shadow: none;
        border: none;
        margin-bottom: 3rem;
        border-bottom: 1px solid #e8dcd6;
    }
    
    .section-card:last-child {
        border-bottom: none;
    }
    
    .section-title {
        font-size: 1.8rem;
        font-weight: 700;
        color: #6d4c41;
        margin-bottom: 1.5rem;
        padding-bottom: 0;
        border-bottom: none;
        position: relative;
        padding-left: 1.5rem;
    }
    
    .section-title::before {
        content: '§';
        position: absolute;
        left: 0;
        color: #a1887f;
        font-size: 1.5rem;
    }
    
    .content-text {
        color: #4e342e;
        line-height: 2;
        margin-bottom: 1.5rem;
        font-size: 1.05rem;
        text-align: justify;
    }
    
    .content-list {
        list-style: none;
        padding: 0;
        margin: 1.5rem 0;
    }
    
    .content-list li {
        padding: 0.9rem 1.5rem 0.9rem 2.5rem;
        margin: 0.75rem 0;
        background: transparent;
        border-radius: 0;
        border-left: 3px solid #a1887f;
        color: #4e342e;
        position: relative;
    }
    
    .content-list li::before {
        content: '✓';
        position: absolute;
        left: 0.5rem;
        color: #a1887f;
        font-weight: 700;
    }
    
    .content-list li strong {
        color: #6d4c41;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.5rem;
        margin: 2rem 0;
    }
    
    .info-card {
        background: #f9f7f5;
        border-radius: 8px;
        padding: 1.5rem;
        border: 2px solid #e8dcd6;
        border-left: 4px solid #a1887f;
        transition: all 0.3s ease;
    }
    
    .info-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 16px rgba(161, 136, 127, 0.15);
    }
    
    .info-card-icon {
        font-size: 2rem;
        margin-bottom: 0.75rem;
    }
    
    .info-card-title {
        font-size: 1rem;
        font-weight: 700;
        color: #6d4c41;
        margin-bottom: 0.5rem;
    }
    
    .info-card-text {
        color: #5d4037;
        font-size: 0.95rem;
        line-height: 1.6;
    }
    
    .tip-box {
        background: #f5f0eb;
        border-radius: 4px;
        padding: 1.5rem;
        margin: 2rem 0;
        border-left: 5px solid #a1887f;
        position: relative;
        font-size: 0.95rem;
        line-height: 1.8;
    }
    
    .tip-box-title {
        font-weight: 700;
        color: #6d4c41;
        margin-bottom: 0.75rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 1.05rem;
    }
    
    .tip-box-text {
        color: #5d4037;
        line-height: 1.8;
    }
    
    .tip-box-text strong {
        color: #6d4c41;
    }
    
    .warning-box {
        background: #fff5f5;
        border-radius: 4px;
        padding: 1.5rem;
        margin: 2rem 0;
        border-left: 5px solid #e57373;
        position: relative;
        font-size: 0.95rem;
        line-height: 1.8;
    }
    
    .warning-box-title {
        font-weight: 700;
        color: #c62828;
        margin-bottom: 0.75rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 1.05rem;
    }
    
    .warning-box-text {
        color: #5d4037;
        line-height: 1.8;
    }

    .book-footer {
        text-align: center;
        margin-top: 4rem;
        padding-top: 2rem;
        border-top: 1px solid #d7ccc8;
        color: #a1887f;
        font-size: 0.9rem;
        font-style: italic;
    }
</style>

<div id="main-content-wrap" class="sidebar-content-wrap">
    <div class="book-wrapper">

        <!-- BOOK HEADER -->
        <div class="book-header">
            <div class="book-title">
                @isset($currentBreeds)
                    @if(count($currentBreeds) > 0)
                        @php
                            $uniqueBreeds = [];
                            $breedIds = [];

                            foreach ($currentBreeds as $breed) {
                                if (!in_array($breed->id, $breedIds)) {
                                    $uniqueBreeds[] = $breed;
                                    $breedIds[] = $breed->id;
                                }
                            }
                        @endphp

                        @if(count($uniqueBreeds) === 1)
                            {{ $uniqueBreeds[0]->name }} Learn Book
                        @else
                            {{ $uniqueBreeds[0]->name }} Learn Book
                        @endif
                    @else
                        {{ $currentBreed->name ?? 'Quail Farming' }} Learn Book
                    @endif
                @else
                    {{ $currentBreed->name ?? 'Quail Farming' }} Learn Book
                @endisset
            </div>

            <div class="book-subtitle">A Comprehensive Farming Guide</div>

            @isset($currentBreeds)
                @if(count($currentBreeds) > 0)
                    @php
                        $uniqueBreeds = [];
                        $breedIds = [];

                        foreach ($currentBreeds as $breed) {
                            if (!in_array($breed->id, $breedIds)) {
                                $uniqueBreeds[] = $breed;
                                $breedIds[] = $breed->id;
                            }
                        }
                    @endphp

                    <div style="margin-top: 1rem; display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                        @foreach($uniqueBreeds as $index => $breed)
                            <div style="padding: 0.75rem; background: linear-gradient(135deg, #fff9f5 0%, #fff7f2 100%); border-radius: 8px; border-left: 3px solid #a1887f; font-size: 0.85rem;">
                                <p style="margin: 0 0 0.25rem 0; color: #8d6e63; font-weight: 600;">
                                    Breed #{{ $index + 1 }}
                                </p>

                                <p style="margin: 0; color: #6d4c41; font-weight: 700;">
                                    {{ $breed->name }}
                                </p>

                                <p style="margin: 0.25rem 0 0; color: #8d6e63; font-size: 0.75rem;">
                                    {{ $breed->egg_production_rate }} eggs/year
                                </p>
                            </div>
                        @endforeach
                    </div>
                @endif
            @endisset
        </div>

        <!-- BOOK CONTENT -->
        <div class="book-content">

            <!-- TABLE OF CONTENTS -->
            <div class="book-toc">
                <div class="toc-title">Talaan ng Nilalaman</div>

                <ul class="toc-list">

                    @if($currentBreed->name === 'Pharaoh Quail')

                        <li class="toc-item"><a href="#chapter1" class="toc-link">Kabanata 1: Panimula</a></li>
                        <li class="toc-item"><a href="#chapter2" class="toc-link">Kabanata 2: Scientific Name at Key Specifications</a></li>
                        <li class="toc-item"><a href="#chapter3" class="toc-link">Kabanata 3: Katangian ng Pharaoh Quail</a></li>
                        <li class="toc-item"><a href="#chapter4" class="toc-link">Kabanata 4: Produksyon ng Itlog</a></li>
                        <li class="toc-item"><a href="#chapter5" class="toc-link">Kabanata 5: Pagpapakain at Nutrisyon</a></li>
                        <li class="toc-item"><a href="#chapter6" class="toc-link">Kabanata 6: Kulungan at Pangangalaga</a></li>
                        <li class="toc-item"><a href="#chapter7" class="toc-link">Kabanata 7: Kalusugan at Pag-iwas sa Sakit</a></li>
                        <li class="toc-item"><a href="#chapter8" class="toc-link">Kabanata 8: Pagpaparami (Breeding)</a></li>
                        <li class="toc-item"><a href="#chapter9" class="toc-link">Kabanata 9: Negosyo at Kita</a></li>
                        <li class="toc-item"><a href="#chapter10" class="toc-link">Kabanata 10: Konklusyon</a></li>

                    @elseif($currentBreed->name === 'Japanese Quail (Coturnix Japonica)')

                        <li class="toc-item"><a href="#chapter1" class="toc-link">Kabanata 1: Panimula</a></li>
                        <li class="toc-item"><a href="#chapter2" class="toc-link">Kabanata 2: Scientific Name at Key Specifications</a></li>
                        <li class="toc-item"><a href="#chapter3" class="toc-link">Kabanata 3: Katangian ng Japanese Quail</a></li>
                        <li class="toc-item"><a href="#chapter4" class="toc-link">Kabanata 4: Produksyon ng Itlog</a></li>
                        <li class="toc-item"><a href="#chapter5" class="toc-link">Kabanata 5: Pagpapakain at Nutrisyon</a></li>
                        <li class="toc-item"><a href="#chapter6" class="toc-link">Kabanata 6: Kulungan at Pangangalaga</a></li>
                        <li class="toc-item"><a href="#chapter7" class="toc-link">Kabanata 7: Kalusugan at Pag-iwas sa Sakit</a></li>
                        <li class="toc-item"><a href="#chapter8" class="toc-link">Kabanata 8: Pagpaparami (Breeding)</a></li>
                        <li class="toc-item"><a href="#chapter9" class="toc-link">Kabanata 9: Negosyo at Kita</a></li>
                        <li class="toc-item"><a href="#chapter10" class="toc-link">Kabanata 10: Konklusyon</a></li>

                    @else

                        <li class="toc-item"><a href="#chapter1" class="toc-link">Kabanata 1: Panimula</a></li>
                        <li class="toc-item"><a href="#chapter2" class="toc-link">Kabanata 2: Scientific Name at Key Specifications</a></li>
                        <li class="toc-item"><a href="#chapter3" class="toc-link">Kabanata 3: Katangian ng Taiwan Brown Line</a></li>
                        <li class="toc-item"><a href="#chapter4" class="toc-link">Kabanata 4: Produksyon ng Itlog</a></li>
                        <li class="toc-item"><a href="#chapter5" class="toc-link">Kabanata 5: Pagpapakain at Nutrisyon</a></li>
                        <li class="toc-item"><a href="#chapter6" class="toc-link">Kabanata 6: Kulungan at Pangangalaga</a></li>
                        <li class="toc-item"><a href="#chapter7" class="toc-link">Kabanata 7: Kalusugan at Pag-iwas sa Sakit</a></li>
                        <li class="toc-item"><a href="#chapter8" class="toc-link">Kabanata 8: Pagpaparami (Breeding)</a></li>
                        <li class="toc-item"><a href="#chapter9" class="toc-link">Kabanata 9: Negosyo at Kita</a></li>
                        <li class="toc-item"><a href="#chapter10" class="toc-link">Kabanata 10: Mga Tips para sa Tagumpay</a></li>
                        <li class="toc-item"><a href="#chapter11" class="toc-link">Kabanata 11: Konklusyon</a></li>

                    @endif

                </ul>
            </div>

            @if($currentBreed->name === 'Pharaoh Quail')

                <!-- PHARAOH QUAIL CONTENT -->

                <div id="chapter1" class="chapter-marker">
                    <div class="chapter-number">Kabanata 1</div>
                    <div class="chapter-title">Panimula</div>
                </div>

                <div class="section-card">
                    <p class="content-text">
                        Ang Pharaoh Quail ay isang uri ng pugo na kilala sa kulay nitong kayumanggi at sa kakayahan nitong mag-produce ng parehong itlog at karne. Ito ay itinuturing na isang strain ng Japanese quail na pinalaki upang maging mas malaki at mas angkop sa meat production, habang nananatili pa rin ang magandang kakayahan sa pangingitlog.
                    </p>
                </div>

                <div id="chapter2" class="chapter-marker">
                    <div class="chapter-number">Kabanata 2</div>
                    <div class="chapter-title">Scientific Name at Key Specifications</div>
                </div>

                <div class="section-card">
                    <h2 class="section-title">Scientific Name</h2>
                    <p class="content-text"><strong><em>Coturnix japonica (Pharaoh strain)</em></strong></p>

                    <h2 class="section-title">Key Specifications</h2>

                    <ul class="content-list">
                        <li><strong>Common Name:</strong> Pharaoh Quail</li>
                        <li><strong>Breed Type:</strong> Brown-colored strain ng Japanese quail</li>
                        <li><strong>Layunin:</strong> Dual-purpose (para sa itlog at karne)</li>
                        <li><strong>Edad ng Pangingitlog:</strong> 35–45 araw</li>
                        <li><strong>Produksyon ng Itlog:</strong> 200–280 itlog kada taon</li>
                        <li><strong>Bigat (Adult):</strong> 150–200 grams</li>
                        <li><strong>Habang Buhay (Lifespan):</strong> 2–3 taon</li>
                        <li><strong>Feed Consumption:</strong> ~20–30 grams/araw bawat pugo</li>
                        <li><strong>Kulay:</strong> Madilim na kayumanggi na may pattern</li>
                        <li><strong>Temperament:</strong> Aktibo ngunit madaling alagaan</li>
                        <li><strong>Climate Adaptability:</strong> Angkop sa tropikal na klima</li>
                    </ul>
                </div>

                <div id="chapter3" class="chapter-marker">
                    <div class="chapter-number">Kabanata 3</div>
                    <div class="chapter-title">Katangian ng Pharaoh Quail</div>
                </div>

                <div class="section-card">
                    <p class="content-text">
                        Ang Pharaoh Quail ay kilala sa pagiging mas malaki kumpara sa ibang uri ng Japanese quail.
                    </p>

                    <ul class="content-list">
                        <li>Mas mabigat kaya mas angkop sa meat production</li>
                        <li>May magandang egg-laying capacity</li>
                        <li>Matibay at kayang mag-adapt sa iba't ibang environment</li>
                        <li>Mas mabilis lumaki kumpara sa ibang pugo</li>
                    </ul>
                </div>

                <div id="chapter4" class="chapter-marker">
                    <div class="chapter-number">Kabanata 4</div>
                    <div class="chapter-title">Produksyon ng Itlog</div>
                </div>

                <div class="section-card">
                    <p class="content-text">
                        Bagama't kilala sa karne, maayos din ang produksyon ng itlog nito.
                    </p>

                    <ul class="content-list">
                        <li>Nagsisimula sa edad na 35–45 araw</li>
                        <li>Nakakapag-produce ng 200–280 itlog kada taon</li>
                        <li>Ang itlog ay may mataas na nutritional value</li>
                    </ul>

                    <div class="tip-box">
                        <div class="tip-box-title">Tip</div>
                        <div class="tip-box-text">
                            Panatilihin ang consistent na ilaw at tamang nutrisyon para sa tuloy-tuloy na produksyon.
                        </div>
                    </div>
                </div>

                <div id="chapter5" class="chapter-marker">
                    <div class="chapter-number">Kabanata 5</div>
                    <div class="chapter-title">Pagpapakain at Nutrisyon</div>
                </div>

                <div class="section-card">
                    <p class="content-text">
                        Upang mapanatili ang kalusugan at produksyon:
                    </p>

                    <h2 class="section-title">Uri ng Pagkain</h2>

                    <ul class="content-list">
                        <li>Starter feeds (para sa sisiw)</li>
                        <li>Grower feeds (para sa lumalaki)</li>
                        <li>Layer o finisher feeds (depende sa layunin)</li>
                    </ul>

                    <h2 class="section-title">Mahahalagang Nutrients</h2>

                    <ul class="content-list">
                        <li>Protein (18–24%)</li>
                        <li>Calcium (lalo na sa layers)</li>
                        <li>Malinis at sapat na tubig</li>
                    </ul>
                </div>

                <div id="chapter6" class="chapter-marker">
                    <div class="chapter-number">Kabanata 6</div>
                    <div class="chapter-title">Kulungan at Pangangalaga</div>
                </div>

                <div class="section-card">
                    <p class="content-text">
                        Ang tamang setup ng kulungan ay mahalaga:
                    </p>

                    <ul class="content-list">
                        <li>Maayos na airflow o bentilasyon</li>
                        <li>Hindi masyadong siksikan</li>
                        <li>Protektado sa ulan at init</li>
                        <li>Regular na paglilinis</li>
                    </ul>

                    <div class="tip-box">
                        <div class="tip-box-title">Rekomendasyon</div>
                        <div class="tip-box-text">
                            Wire cages para sa mas efficient na management at egg collection.
                        </div>
                    </div>
                </div>

                <div id="chapter7" class="chapter-marker">
                    <div class="chapter-number">Kabanata 7</div>
                    <div class="chapter-title">Kalusugan at Pag-iwas sa Sakit</div>
                </div>

                <div class="section-card">
                    <p class="content-text">
                        Karaniwang matibay ang Pharaoh Quail ngunit kailangan pa rin ng tamang pangangalaga.
                    </p>

                    <h2 class="section-title">Mga Dapat Bantayan</h2>

                    <ul class="content-list">
                        <li>Stress dahil sa init o siksikan</li>
                        <li>Kakulangan sa nutrisyon</li>
                        <li>Mga impeksyon</li>
                    </ul>

                    <h2 class="section-title">Pag-iwas</h2>

                    <ul class="content-list">
                        <li>Panatilihin ang kalinisan</li>
                        <li>Iwasan ang biglaang pagbabago sa feeds</li>
                        <li>Siguraduhin ang sapat na tubig at maayos na kapaligiran</li>
                    </ul>
                </div>

                <div id="chapter8" class="chapter-marker">
                    <div class="chapter-number">Kabanata 8</div>
                    <div class="chapter-title">Pagpaparami (Breeding)</div>
                </div>

                <div class="section-card">
                    <p class="content-text">Para sa pagpaparami:</p>

                    <ul class="content-list">
                        <li>Ratio: 1 lalaki : 3–4 babae</li>
                        <li>Incubation period: 17–18 araw</li>
                        <li>Temperature: ~37.5°C</li>
                        <li>Humidity: 60–70%</li>
                    </ul>
                </div>

                <div id="chapter9" class="chapter-marker">
                    <div class="chapter-number">Kabanata 9</div>
                    <div class="chapter-title">Negosyo at Kita</div>
                </div>

                <div class="section-card">
                    <p class="content-text">
                        Ang Pharaoh Quail ay magandang pagpipilian para sa negosyo dahil sa dual-purpose nito.
                    </p>

                    <h2 class="section-title">Mga Produkto</h2>

                    <ul class="content-list">
                        <li>Itlog ng pugo</li>
                        <li>Karne ng pugo</li>
                        <li>Sisiw</li>
                    </ul>

                    <h2 class="section-title">Mga Benepisyo</h2>

                    <ul class="content-list">
                        <li>Mas malaking timbang = mas mataas na kita sa karne</li>
                        <li>Stable na egg production</li>
                        <li>Mabilis ang paglaki</li>
                    </ul>
                </div>

                <div id="chapter10" class="chapter-marker">
                    <div class="chapter-number">Kabanata 10</div>
                    <div class="chapter-title">Konklusyon</div>
                </div>

                <div class="section-card">
                    <p class="content-text">
                        Ang Pharaoh Quail ay isang praktikal at kapaki-pakinabang na uri ng pugo para sa mga nais pagsabayin ang produksyon ng itlog at karne. Sa tamang pag-aalaga at kaalaman, maaari itong maging isang matatag na pinagkakakitaan.
                    </p>

                    <div class="tip-box" style="text-align: center; margin-top: 3rem;">
                        <div class="tip-box-title">Pangako</div>

                        <div class="tip-box-text">
                            <em>"Sa wastong pag-aalaga, ang Pharaoh Quail ay hindi lamang alaga—ito ay isang oportunidad."</em>
                        </div>
                    </div>
                </div>

            @elseif($currentBreed->name === 'Japanese Quail (Coturnix Japonica)')

                <!-- JAPANESE QUAIL CONTENT -->

                <div id="chapter1" class="chapter-marker">
                    <div class="chapter-number">Kabanata 1</div>
                    <div class="chapter-title">Panimula</div>
                </div>

                <div class="section-card">
                    <p class="content-text">
                        Ang <strong>Japanese Quail (Coturnix japonica)</strong> ay ang pinakakaraniwang uri ng pugo na inaalagaan sa Pilipinas. Kilala ito sa pagiging matibay, mabilis lumaki, at mataas ang produksyon ng itlog. Dahil sa mga katangiang ito, ito ang pangunahing pinipili ng mga magsasaka at negosyante sa industriya ng poultry.
                    </p>
                </div>

                <div id="chapter2" class="chapter-marker">
                    <div class="chapter-number">Kabanata 2</div>
                    <div class="chapter-title">Scientific Name at Key Specifications</div>
                </div>

                <div class="section-card">
                    <h2 class="section-title">Scientific Name</h2>
                    <p class="content-text"><strong><em>Coturnix japonica</em></strong></p>

                    <h2 class="section-title">Key Specifications</h2>

                    <ul class="content-list">
                        <li><strong>Common Name:</strong> Japanese Quail</li>
                        <li><strong>Breed Type:</strong> Commercial quail breed</li>
                        <li><strong>Layunin:</strong> Pangunahing para sa egg production (pwede rin sa karne)</li>
                        <li><strong>Edad ng Pangingitlog:</strong> 35–45 araw</li>
                        <li><strong>Produksyon ng Itlog:</strong> 250–300 itlog kada taon</li>
                        <li><strong>Bigat (Adult):</strong> 120–180 grams</li>
                        <li><strong>Habang Buhay (Lifespan):</strong> 2–3 taon</li>
                        <li><strong>Feed Consumption:</strong> ~20–25 grams/araw bawat pugo</li>
                        <li><strong>Kulay:</strong> Kayumanggi na may batik-batik</li>
                        <li><strong>Temperament:</strong> Aktibo ngunit madaling alagaan</li>
                        <li><strong>Climate Adaptability:</strong> Angkop sa mainit at tropikal na klima</li>
                    </ul>
                </div>

                <div id="chapter3" class="chapter-marker">
                    <div class="chapter-number">Kabanata 3</div>
                    <div class="chapter-title">Katangian ng Japanese Quail</div>
                </div>

                <div class="section-card">
                    <p class="content-text">
                        Ang Japanese Quail ay kilala bilang pinaka-versatile na uri ng pugo.
                    </p>

                    <ul class="content-list">
                        <li>Pinakamataas ang egg production kumpara sa ibang breeds</li>
                        <li>Mabilis ang paglaki (early maturity)</li>
                        <li>Matibay at hindi madaling magkasakit</li>
                        <li>Madaling alagaan kahit sa maliit na espasyo</li>
                    </ul>
                </div>

                <div id="chapter4" class="chapter-marker">
                    <div class="chapter-number">Kabanata 4</div>
                    <div class="chapter-title">Produksyon ng Itlog</div>
                </div>

                <div class="section-card">
                    <p class="content-text">
                        Ito ang pangunahing dahilan kung bakit popular ang Japanese Quail.
                    </p>

                    <ul class="content-list">
                        <li>Nagsisimula sa edad na <strong>35–45 araw</strong></li>
                        <li>Kayang mag-produce ng <strong>250–300 itlog kada taon</strong></li>
                        <li>Mataas ang nutritional value ng itlog</li>
                    </ul>

                    <div class="tip-box">
                        <div class="tip-box-title">Tip</div>

                        <div class="tip-box-text">
                            Panatilihin ang 14–16 oras na ilaw bawat araw para sa tuloy-tuloy na pangingitlog.
                        </div>
                    </div>
                </div>

                <div id="chapter5" class="chapter-marker">
                    <div class="chapter-number">Kabanata 5</div>
                    <div class="chapter-title">Pagpapakain at Nutrisyon</div>
                </div>

                <div class="section-card">
                    <p class="content-text">
                        Ang tamang nutrisyon ay mahalaga sa produksyon.
                    </p>

                    <h2 class="section-title">Uri ng Pagkain</h2>

                    <ul class="content-list">
                        <li>Starter feeds (0–3 weeks)</li>
                        <li>Grower feeds (3–5 weeks)</li>
                        <li>Layer feeds (para sa nangingitlog)</li>
                    </ul>

                    <h2 class="section-title">Mahahalagang Nutrients</h2>

                    <ul class="content-list">
                        <li>Protein (18–24%)</li>
                        <li>Calcium (para sa shell ng itlog)</li>
                        <li>Malinis at sapat na tubig</li>
                    </ul>
                </div>

                <div id="chapter6" class="chapter-marker">
                    <div class="chapter-number">Kabanata 6</div>
                    <div class="chapter-title">Kulungan at Pangangalaga</div>
                </div>

                <div class="section-card">
                    <p class="content-text">
                        Upang mapanatili ang kalusugan:
                    </p>

                    <ul class="content-list">
                        <li>Maayos na bentilasyon</li>
                        <li>Hindi siksikan</li>
                        <li>Protektado sa init at ulan</li>
                        <li>Regular na paglilinis</li>
                    </ul>

                    <div class="tip-box">
                        <div class="tip-box-title">Rekomendasyon</div>

                        <div class="tip-box-text">
                            Wire cage system para sa mas efficient na management.
                        </div>
                    </div>
                </div>

                <div id="chapter7" class="chapter-marker">
                    <div class="chapter-number">Kabanata 7</div>
                    <div class="chapter-title">Kalusugan at Pag-iwas sa Sakit</div>
                </div>

                <div class="section-card">
                    <p class="content-text">
                        Ang Japanese Quail ay kilalang matibay, ngunit kailangan pa rin ng pag-iingat.
                    </p>

                    <h2 class="section-title">Mga Dapat Bantayan</h2>

                    <ul class="content-list">
                        <li>Stress (init, siksikan)</li>
                        <li>Kakulangan sa nutrisyon</li>
                        <li>Impeksyon</li>
                    </ul>

                    <h2 class="section-title">Pag-iwas</h2>

                    <ul class="content-list">
                        <li>Panatilihin ang kalinisan</li>
                        <li>Iwasan ang biglaang pagbabago ng feeds</li>
                        <li>Siguraduhing sapat ang tubig at bentilasyon</li>
                    </ul>
                </div>

                <div id="chapter8" class="chapter-marker">
                    <div class="chapter-number">Kabanata 8</div>
                    <div class="chapter-title">Pagpaparami (Breeding)</div>
                </div>

                <div class="section-card">
                    <p class="content-text">Para sa pagpaparami:</p>

                    <ul class="content-list">
                        <li>Ratio: <strong>1 lalaki : 3–4 babae</strong></li>
                        <li>Incubation period: 17–18 araw</li>
                        <li>Temperature: ~37.5°C</li>
                        <li>Humidity: 60–70%</li>
                    </ul>
                </div>

                <div id="chapter9" class="chapter-marker">
                    <div class="chapter-number">Kabanata 9</div>
                    <div class="chapter-title">Negosyo at Kita</div>
                </div>

                <div class="section-card">
                    <p class="content-text">
                        Ang Japanese Quail ay isa sa pinaka-profitable na poultry.
                    </p>

                    <h2 class="section-title">Mga Produkto</h2>

                    <ul class="content-list">
                        <li>Itlog ng pugo</li>
                        <li>Karne ng pugo</li>
                        <li>Sisiw</li>
                    </ul>

                    <h2 class="section-title">Mga Benepisyo</h2>

                    <ul class="content-list">
                        <li>Mataas ang demand sa itlog</li>
                        <li>Mabilis ang ROI</li>
                        <li>Madaling alagaan kahit small-scale</li>
                    </ul>
                </div>

                <div id="chapter10" class="chapter-marker">
                    <div class="chapter-number">Kabanata 10</div>
                    <div class="chapter-title">Konklusyon</div>
                </div>

                <div class="section-card">
                    <p class="content-text">
                        Ang <strong>Japanese Quail (Coturnix japonica)</strong> ay isang mahusay na panimulang uri ng pugo para sa mga nais magnegosyo. Sa tamang sistema at pag-aalaga, ito ay nagbibigay ng tuloy-tuloy na kita at mataas na produksyon.
                    </p>

                    <div class="tip-box" style="text-align: center; margin-top: 3rem;">
                        <div class="tip-box-title">Pangako</div>

                        <div class="tip-box-text">
                            <em>"Sa pagiging simple at matibay, ang Japanese Quail ay nananatiling isang mahalagang uri ng pugo sa industriya."</em>
                        </div>
                    </div>
                </div>

            @else

                <!-- TAIWAN BROWN LINE CONTENT -->

                <div id="chapter1" class="chapter-marker">
                    <div class="chapter-number">Kabanata 1</div>
                    <div class="chapter-title">Panimula</div>
                </div>

                <div class="section-card">
                    <p class="content-text">
                        Ang <strong>Japanese Coturnix Crossbreed (Taiwan Brown Line)</strong> ay isang uri ng pugo na karaniwang inaalagaan para sa produksyon ng itlog at karne. Kilala ito sa mabilis nitong paglaki, mataas na produksyon ng itlog, at kakayahang umangkop sa klima ng Pilipinas. Dahil dito, isa ito sa mga pinakapopular na lahi ng pugo para sa mga nagbabalak magsimula ng maliit hanggang katamtamang negosyo.
                    </p>
                </div>

                <div id="chapter2" class="chapter-marker">
                    <div class="chapter-number">Kabanata 2</div>
                    <div class="chapter-title">Scientific Name at Key Specifications</div>
                </div>

                <div class="section-card">
                    <h2 class="section-title">Scientific Name</h2>
                    <p class="content-text"><strong><em>Coturnix japonica</em></strong></p>

                    <h2 class="section-title">Key Specifications</h2>

                    <ul class="content-list">
                        <li><strong>Common Name:</strong> Japanese Quail / Coturnix Quail</li>
                        <li><strong>Breed Type:</strong> Crossbreed (Taiwan Brown Line)</li>
                        <li><strong>Layunin:</strong> Egg production at meat production</li>
                        <li><strong>Edad ng Pangingitlog:</strong> 35–45 araw</li>
                        <li><strong>Produksyon ng Itlog:</strong> 250–300 itlog kada taon</li>
                        <li><strong>Bigat (Adult):</strong> 120–180 grams</li>
                        <li><strong>Habang Buhay (Lifespan):</strong> 2–3 taon</li>
                        <li><strong>Feed Consumption:</strong> ~20–25 grams/araw bawat pugo</li>
                        <li><strong>Kulay:</strong> Kayumanggi na may batik-batik</li>
                        <li><strong>Temperament:</strong> Kalmado at madaling alagaan</li>
                        <li><strong>Climate Adaptability:</strong> Angkop sa mainit at tropikal na klima</li>
                    </ul>
                </div>

                <div id="chapter3" class="chapter-marker">
                    <div class="chapter-number">Kabanata 3</div>
                    <div class="chapter-title">Katangian ng Taiwan Brown Line</div>
                </div>

                <div class="section-card">
                    <p class="content-text">
                        Ang Taiwan Brown Line ay isang crossbreed mula sa Japanese quail na pinahusay upang maging mas produktibo. Narito ang ilan sa mga katangian nito:
                    </p>

                    <ul class="content-list">
                        <li>Mas mataas ang egg production kumpara sa ibang linya</li>
                        <li>Mas uniform ang laki at timbang</li>
                        <li>Mas matibay sa stress at klima</li>
                        <li>Mas efficient sa feed conversion</li>
                    </ul>
                </div>

                <div id="chapter4" class="chapter-marker">
                    <div class="chapter-number">Kabanata 4</div>
                    <div class="chapter-title">Produksyon ng Itlog</div>
                </div>

                <div class="section-card">
                    <p class="content-text">
                        Isa sa pinakamahalagang dahilan kung bakit ito inaalagaan ay ang mataas nitong produksyon ng itlog.
                    </p>

                    <ul class="content-list">
                        <li>Nagsisimulang mangitlog sa edad na <strong>35–45 araw</strong></li>
                        <li>Kayang mag-produce ng <strong>250–300 itlog kada taon</strong></li>
                        <li>Maliit ngunit masustansya ang itlog</li>
                    </ul>

                    <div class="tip-box">
                        <div class="tip-box-title">Tip</div>

                        <div class="tip-box-text">
                            Panatilihing may sapat na ilaw (14–16 hours light exposure) para tuloy-tuloy ang pangingitlog.
                        </div>
                    </div>
                </div>

                <div id="chapter5" class="chapter-marker">
                    <div class="chapter-number">Kabanata 5</div>
                    <div class="chapter-title">Pagpapakain at Nutrisyon</div>
                </div>

                <div class="section-card">
                    <p class="content-text">
                        Mahalaga ang tamang pagkain upang makamit ang maximum na produksyon.
                    </p>

                    <h2 class="section-title">Uri ng Pagkain</h2>

                    <ul class="content-list">
                        <li>Starter feeds (0–3 weeks)</li>
                        <li>Grower feeds (3–5 weeks)</li>
                        <li>Layer feeds (5 weeks pataas)</li>
                    </ul>

                    <h2 class="section-title">Dagdag na Nutrisyon</h2>

                    <ul class="content-list">
                        <li>Calcium (para sa matibay na shell ng itlog)</li>
                        <li>Protein (18–24% depende sa stage)</li>
                        <li>Malinis na tubig (palaging available)</li>
                    </ul>
                </div>

                <div id="chapter6" class="chapter-marker">
                    <div class="chapter-number">Kabanata 6</div>
                    <div class="chapter-title">Kulungan at Pangangalaga</div>
                </div>

                <div class="section-card">
                    <p class="content-text">
                        Ang tamang kulungan ay nakakatulong sa kalusugan ng pugo.
                    </p>

                    <h2 class="section-title">Mga Dapat Isaalang-alang</h2>

                    <ul class="content-list">
                        <li>Maayos na bentilasyon</li>
                        <li>Sapat na espasyo (hindi siksikan)</li>
                        <li>Proteksyon laban sa ulan at init</li>
                        <li>Regular na paglilinis</li>
                    </ul>

                    <div class="tip-box">
                        <div class="tip-box-title">Rekomendasyon</div>

                        <div class="tip-box-text">
                            Gumamit ng wire cage system para mas madaling linisin at mangolekta ng itlog.
                        </div>
                    </div>
                </div>

                <div id="chapter7" class="chapter-marker">
                    <div class="chapter-number">Kabanata 7</div>
                    <div class="chapter-title">Kalusugan at Pag-iwas sa Sakit</div>
                </div>

                <div class="section-card">
                    <p class="content-text">
                        Bagama't matibay ang Taiwan Brown Line, kailangan pa rin ng wastong pag-aalaga.
                    </p>

                    <h2 class="section-title">Karaniwang Problema</h2>

                    <ul class="content-list">
                        <li>Stress (dulot ng siksikan o init)</li>
                        <li>Kakulangan sa nutrisyon</li>
                        <li>Impeksyon</li>
                    </ul>

                    <h2 class="section-title">Pag-iwas</h2>

                    <ul class="content-list">
                        <li>Panatilihing malinis ang kapaligiran</li>
                        <li>Iwasan ang biglaang pagbabago ng pagkain</li>
                        <li>Siguraduhing may sapat na tubig at bentilasyon</li>
                    </ul>
                </div>

                <div id="chapter8" class="chapter-marker">
                    <div class="chapter-number">Kabanata 8</div>
                    <div class="chapter-title">Pagpaparami (Breeding)</div>
                </div>

                <div class="section-card">
                    <p class="content-text">
                        Kung nais mong paramihin ang iyong alaga:
                    </p>

                    <ul class="content-list">
                        <li>Ratio: <strong>1 lalaki : 3–4 babae</strong></li>
                        <li>Gumamit ng incubator para sa mas mataas na hatch rate</li>
                        <li>Temperature: ~37.5°C</li>
                        <li>Humidity: 60–70%</li>
                    </ul>
                </div>

                <div id="chapter9" class="chapter-marker">
                    <div class="chapter-number">Kabanata 9</div>
                    <div class="chapter-title">Negosyo at Kita</div>
                </div>

                <div class="section-card">
                    <p class="content-text">
                        Ang pag-aalaga ng Taiwan Brown Line ay may malaking potensyal na pagkakakitaan.
                    </p>

                    <h2 class="section-title">Mga Produkto</h2>

                    <ul class="content-list">
                        <li>Itlog ng pugo</li>
                        <li>Karne ng pugo</li>
                        <li>Sisiw (day-old chicks)</li>
                    </ul>

                    <h2 class="section-title">Mga Benepisyo</h2>

                    <ul class="content-list">
                        <li>Mabilis ang ROI (Return on Investment)</li>
                        <li>Maliit lang ang puhunan</li>
                        <li>Mataas ang demand sa merkado</li>
                    </ul>
                </div>

                <div id="chapter10" class="chapter-marker">
                    <div class="chapter-number">Kabanata 10</div>
                    <div class="chapter-title">Mga Tips para sa Tagumpay</div>
                </div>

                <div class="section-card">
                    <ul class="content-list">
                        <li>Magsimula sa maliit, saka palakihin</li>
                        <li>Mag-record ng produksyon</li>
                        <li>Humanap ng tamang supplier ng feeds</li>
                        <li>Panatilihin ang kalidad ng produkto</li>
                    </ul>
                </div>

                <div id="chapter11" class="chapter-marker">
                    <div class="chapter-number">Kabanata 11</div>
                    <div class="chapter-title">Konklusyon</div>
                </div>

                <div class="section-card">
                    <p class="content-text">
                        Ang <strong>Japanese Coturnix Crossbreed (Taiwan Brown Line)</strong> ay isang mahusay na pagpipilian para sa mga nais magsimula ng poultry business. Sa tamang kaalaman, wastong pag-aalaga, at dedikasyon, maaari itong maging isang matagumpay at tuloy-tuloy na pinagkakakitaan.
                    </p>

                    <div class="tip-box" style="text-align: center; margin-top: 3rem;">
                        <div class="tip-box-title">Pangako</div>

                        <div class="tip-box-text">
                            <em>"Sa tamang kaalaman at sipag, ang maliit na pugo ay maaaring maging malaking oportunidad."</em>
                        </div>
                    </div>
                </div>

            @endif

            <div class="book-footer">
                <p>© {{ date('Y') }} Escalona's Quail Farm - {{ $currentBreed->name }} Learn Book</p>
                <p>Last Updated: {{ now()->format('F d, Y') }}</p>
            </div>

        </div>
    </div>
</div>

<!-- Notification System Scripts -->
<link rel="stylesheet" href="{{ asset('notification-styles.css') }}">
<script src="{{ asset('notification-system.js') }}"></script>
<script src="{{ asset('notification-helpers.js') }}"></script>
<script src="{{ asset('notification-events.js') }}"></script>

<script>
document.querySelectorAll('.toc-link').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();

        const href = this.getAttribute('href');
        const element = document.querySelector(href);

        if (element) {
            element.scrollIntoView({ behavior: 'smooth' });
        }
    });
});

document.addEventListener('DOMContentLoaded', function() {
    if (typeof notifyNavigation === 'function') {
        notifyNavigation('Learn Book', '📚');
    }
});
</script>

<script>
const NOTIFICATION_STORAGE_KEY = 'squifm_notifications';

function getNotifications() {
    try {
        return JSON.parse(localStorage.getItem(NOTIFICATION_STORAGE_KEY) || '[]');
    } catch {
        return [];
    }
}

function saveNotifications(notifications) {
    localStorage.setItem(
        NOTIFICATION_STORAGE_KEY,
        JSON.stringify(notifications)
    );
}

function getSeenCount() {
    return parseInt(
        localStorage.getItem('squifm_notifications_seen') || '0',
        10
    );
}

function setSeenCount(count) {
    localStorage.setItem(
        'squifm_notifications_seen',
        count.toString()
    );
}

function renderNotifications() {
    if (window.__squifmBellFeedActive) return;

    const list = document.getElementById('notification-list');
    const badge = document.getElementById('notification-badge');
    const notifications = getNotifications();

    if (!list || !badge) return;

    if (notifications.length === 0) {
        list.innerHTML =
            '<div class="notification-empty">No notifications yet.</div>';

        badge.style.display = 'none';
        badge.classList.remove('has-notifications');

        return;
    }

    const seenCount = getSeenCount();
    const unseenCount = Math.max(
        0,
        notifications.length - seenCount
    );

    if (unseenCount > 0) {
        badge.style.display = 'flex';
        badge.textContent = unseenCount;
        badge.classList.add('has-notifications');
    } else {
        badge.style.display = 'none';
        badge.classList.remove('has-notifications');
    }

    list.innerHTML = '';

    notifications.forEach(n => {
        const item = document.createElement('div');

        item.className =
            'notification-item ' +
            (n.type || 'success');

        item.style.cssText =
            'display: flex; align-items: flex-start; gap: 0.75rem; padding: 0.75rem;';

        item.innerHTML = `
            <span class="notification-icon">${n.icon || '📝'}</span>
            <div class="notification-content">
                <div class="notification-text">${n.message}</div>
                <div class="notification-time">${n.time}</div>
            </div>
        `;

        list.appendChild(item);
    });
}

function toggleNotificationDropdown() {
    const dropdown =
        document.getElementById('notification-dropdown');

    const badge =
        document.getElementById('notification-badge');

    if (dropdown) {
        dropdown.classList.toggle('show');

        if (dropdown.classList.contains('show')) {
            const notifications = getNotifications();

            setSeenCount(notifications.length);

            if (badge) {
                badge.textContent = '0';
                badge.style.display = 'none';
                badge.classList.remove('has-notifications');
            }
        }
    }
}

function clearNotifications(event) {
    event.stopPropagation();

    saveNotifications([]);
    renderNotifications();
}

document.addEventListener('click', function(e) {
    const bell =
        document.getElementById('notification-bell');

    const dropdown =
        document.getElementById('notification-dropdown');

    if (
        bell &&
        dropdown &&
        !bell.contains(e.target)
    ) {
        dropdown.classList.remove('show');
    }
});

document.addEventListener('DOMContentLoaded', function() {
    renderNotifications();
});
</script>

@endsection