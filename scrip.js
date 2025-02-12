document.addEventListener('DOMContentLoaded', () => {
    const surahSelect = document.getElementById('surah-select');
    const ayahSelect = document.getElementById('ayah-select');
    const quranText = document.getElementById('quran-text');
    const prevSurahBtnEn = document.getElementById('prevsurah-bten');
    const prevSurahBtnAr = document.getElementById('prevsurah-btar');
    const nextSurahBtnEn = document.getElementById('nextsurah-bten');
    const nextSurahBtnAr = document.getElementById('nextsurah-btar');
    const prevAyahBtn = document.getElementById('prev-ayah-bten');
    const nextAyahBtn = document.getElementById('next-ayah-bten');
    const languageButtons = document.querySelectorAll('.lang-btn');
    const loadingIndicator = document.getElementById('loading-indicator');

    let currentSurah = 1;
    let currentAyah = 1;
    let currentVerses = [];

    // --- API Functions ---
    async function fetchQuranData(surahNumber) {
        try {
            const url = `https://api.quran.com/api/v4/verses/by_chapter/${surahNumber}?language=en&words=true&translations=131`;
            const response = await fetch(url);

            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }

            const data = await response.json();

            if (!data || !data.verses) {
                throw new Error("Invalid data received from API (no verses).");
            }

            return data.verses;

        } catch (error) {
            console.error("Error fetching Quran data:", error);
            quranText.textContent = `Failed to load Quran data: ${error.message}`;
            return [];
        }
    }

    async function fetchSurahList() {
        try {
            const response = await fetch('https://api.quran.com/api/v4/chapters?language=en');

            if (!response.ok) {
                throw new Error(`HTTP error fetching Surah list! Status: ${response.status}`);
            }

            const data = await response.json();

            if (!data || !data.chapters) {
                throw new Error("Invalid data received from API (no chapters).");
            }
            return data.chapters;

        } catch (error) {
            console.error("Error fetching Surah list:", error);
            quranText.textContent = `Failed to load Surah list: ${error.message}`;
            return [];
        }
    }

    async function populateSurahSelect() {
        const surahs = await fetchSurahList();
        if (surahs && surahs.length > 0) {
            surahs.forEach(surah => {
                const option = document.createElement('option');
                option.value = surah.id;
                option.textContent = `${surah.id}. ${surah.name_simple} (${surah.name_arabic})`;
                surahSelect.appendChild(option);
            });
            // Trigger change event to load initial Surah.
            surahSelect.dispatchEvent(new Event('change'));
        } else {
            quranText.textContent = "No Surahs found.";
        }
    }

    async function displayFullSurah(surahNumber) {
        const verses = await fetchQuranData(surahNumber);

        if (verses && verses.length > 0) {
            let html = '';
            verses.forEach(verse => {
                html += '<p>';
                if (verse.words && Array.isArray(verse.words)) {
                    verse.words.forEach(word => {
                        const wordText = word.text || word.transliteration?.text || word.code || word.word || "???";
                        const translationText = word.translation?.text || "";
                        html += `<span title="${translationText}">${wordText}</span> `;
                    });
                } else {
                    console.warn("Verse is missing 'words' array:", verse);
                    html += '<span>[Missing Word Data]</span>';
                }
                html += ` (${verse.verse_key})</p>`;
            });
            quranText.innerHTML = html;
        } else {
            quranText.textContent = "No verses found for this Surah."; // Handle empty Surahs
        }
    }

    // --- Event Listeners (with alerts for debugging) ---

    if (surahSelect) {
        surahSelect.addEventListener('change', (event) => {
            displayFullSurah(parseInt(event.target.value, 10));
        });
    }

    if (prevSurahBtnEn) {
        prevSurahBtnEn.addEventListener('click', () => {
            if (currentSurah > 1) {
                currentSurah--;
                surahSelect.value = currentSurah;
                surahSelect.dispatchEvent(new Event('change'));
            }
        });
    }

    if (prevSurahBtnAr) {
        prevSurahBtnAr.addEventListener('click', () => {
            if (currentSurah > 1) {
                currentSurah--;
                surahSelect.value = currentSurah;
                surahSelect.dispatchEvent(new Event('change'));
            }
        });
    }

    if (nextSurahBtnEn) {
        nextSurahBtnEn.addEventListener('click', () => {
            if (currentSurah < 114) {
                currentSurah++;
                surahSelect.value = currentSurah;
                surahSelect.dispatchEvent(new Event('change'));
            }
        });
    }

    if (nextSurahBtnAr) {
        nextSurahBtnAr.addEventListener('click', () => {
            if (currentSurah < 114) {
                currentSurah++;
                surahSelect.value = currentSurah;
                surahSelect.dispatchEvent(new Event('change'));
            }
        });
    }

    // --- Initial Setup ---
    populateSurahSelect();
    const savedLanguage = localStorage.getItem('language');
    if (savedLanguage) {
        switchLanguage(savedLanguage);
    } else {
        switchLanguage('en');
    }
});