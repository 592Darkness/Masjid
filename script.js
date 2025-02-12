document.addEventListener('DOMContentLoaded', () => {
    const surahSelect = document.getElementById('surah-select');
    const ayahSelect = document.getElementById('ayah-select');
    const quranText = document.getElementById('quran-text');
    const prevSurahBtn = document.getElementById('prev-surah-bten');
    const nextSurahBtn = document.getElementById('next-surah-bten');
    const prevAyahBtn = document.getElementById('prev-ayah-bten');
    const nextAyahBtn = document.getElementById('next-ayah-bten');
    const languageButtons = document.querySelectorAll('.lang-btn');
    const loadingIndicator = document.getElementById('loading-indicator');

    let currentSurah = 1;
    let currentAyah = 1;
    let currentVerses = [];

    // --- Language Switching ---
    function switchLanguage(lang) {
        const arElements = document.querySelectorAll('.ar');
        const enElements = document.querySelectorAll('.en');

        if (lang === 'ar') {
            arElements.forEach(el => el.style.display = 'block');
            enElements.forEach(el => el.style.display = 'none');
            document.documentElement.setAttribute('lang', 'ar');
            document.documentElement.setAttribute('dir', 'rtl');
        } else {
            arElements.forEach(el => el.style.display = 'none');
            enElements.forEach(el => el.style.display = 'block');
            document.documentElement.setAttribute('lang', 'en');
            document.documentElement.setAttribute('dir', 'ltr');
        }

        languageButtons.forEach(button => {
            if (button.dataset.lang === lang) {
                button.classList.add('active');
            } else {
                button.classList.remove('active');
            }
        });

        localStorage.setItem('language', lang);
        // Redisplay the current ayah to update text direction and content
        if (currentVerses.length > 0) {
            displayAyah(currentAyah);
        }
    }


    languageButtons.forEach(button => {
        button.addEventListener('click', () => {
            switchLanguage(button.dataset.lang);
        });
    });

    // --- API Functions ---
    async function fetchQuranData(surahNumber, ayahNumber = null) {
        loadingIndicator.style.display = 'block';
        try {
            const url = `https://api.quran.com/api/v4/verses/by_chapter/${surahNumber}?language=en&words=true&translations=131`;

            if (ayahNumber) {
                url = `https://api.quran.com/api/v4/verses/by_key/${surahNumber}:${ayahNumber}?language=en&words=true&translations=131`;
            }
            const response = await fetch(url);

            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }

            const data = await response.json();
            const verses = data.verses || (data.verse ? [data.verse] : null);
            if (!verses || verses.length === 0) {
                throw new Error("Invalid data received from API (no verses).");
            }
            return data.verses;

        } catch (error) {
            console.error("Error fetching Quran data:", error);
            quranText.textContent = `Failed to load Quran data: ${error.message}`;
            return [];

        } finally {
            loadingIndicator.style.display = 'none';
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
        if (surahs.length > 0) {
            surahs.forEach(surah => {
                const option = document.createElement('option');
                option.value = surah.id;
                option.textContent = `${surah.id}. ${surah.name_simple} (${surah.name_arabic})`;
                surahSelect.appendChild(option);
            });
            surahSelect.value = currentSurah;
            surahSelect.dispatchEvent(new Event('change'));
        } else {
            quranText.textContent = "No Surahs found.";
        }
    }

    // --- Display Functions ---
    async function displaySurah(surahNumber) {
        currentVerses = await fetchQuranData(surahNumber);
        if (currentVerses) {
            populateAyahSelect(currentVerses);
            displayAyah(1);
        }
    }


    function displayAyah(ayahNumber) {
        const verse = currentVerses.find(v => v.verse_number === ayahNumber);
        if (verse) {
            let html = `<p data-ayah="${verse.verse_number}" dir="${document.documentElement.dir === 'rtl' ? 'rtl' : 'ltr'}">`; // Add data-ayah

            if (verse.words && Array.isArray(verse.words)) {
                verse.words.forEach(word => {
                    const wordText = word.text_uthmani || word.transliteration?.text || word.code || word.word || "???"; // Prioritize Uthmani
                    const translationText = word.translation?.text || "";
					// Add word-id for easier selection
                    html += `<span data-word-id="${word.id}" title="${translationText}">${wordText}</span> `;
                });
            } else {
                console.warn("Verse is missing 'words' array:", verse);
                html += '<span>[Missing Word Data]</span>';
            }
            html += ` (${verse.verse_key})</p>`;
            quranText.innerHTML = html;
            currentAyah = ayahNumber;
            ayahSelect.value = currentAyah;

        } else {
            quranText.textContent = "Ayah not found.";
        }
    }


    function populateAyahSelect(verses) {
        ayahSelect.innerHTML = '';
        verses.forEach(verse => {
            const option = document.createElement('option');
            option.value = verse.verse_number;
            option.textContent = verse.verse_number;
            ayahSelect.appendChild(option);
        });
    }


    // --- Event Listeners ---

    surahSelect.addEventListener('change', (event) => {
        displayFullSurah(parseInt(event.target.value, 10));
    });

    ayahSelect.addEventListener('change', (event) => {
        displayAyah(parseInt(event.target.value, 10));
    });

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

    prevAyahBtn.addEventListener('click', () => {
        if (currentAyah > 1) {
            displayAyah(currentAyah - 1);
        }
    });

    nextAyahBtn.addEventListener('click', () => {
        const nextAyah = currentAyah + 1;
        if (currentVerses.find(v => v.verse_number === nextAyah)) {
            displayAyah(nextAyah);
        }
    });

    // Event Delegation for word clicks
    quranText.addEventListener('click', (event) => {
        if (event.target.tagName === 'SPAN' && event.target.hasAttribute('data-word-id')) {
            const wordId = event.target.dataset.wordId;
            const word = currentVerses.flatMap(v => v.words).find(w => w.id == wordId); // Use flatMap

            if (word) {
                // Example: Display an alert with the word's translation.
                alert(`Translation: ${word.translation.text}`);
                // You could do more here:  play audio, show a modal, etc.
            }
        }
    });


    // --- Initial Setup ---
    populateSurahSelect();
    const savedLanguage = localStorage.getItem('language');
    if (savedLanguage) {
        switchLanguage(savedLanguage);
    } else {
        switchLanguage('en');
    }
});