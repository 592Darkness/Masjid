document.addEventListener('DOMContentLoaded', () => {

    const surahSelect = document.getElementById('surah-select');
    const quranText = document.getElementById('quran-text');
    const prevSurahBtnEn = document.querySelectorAll('[id="prev-surah-btn-en"]');
    const prevSurahBtnAr = document.querySelectorAll('[id="prev-surah-btn-ar"]');
    const nextSurahBtnEn = document.querySelectorAll('[id="next-surah-btn-en"]');
    const nextSurahBtnAr = document.querySelectorAll('[id="next-surah-btn-ar"]');
    const languageButtons = document.querySelectorAll('.lang-btn');

    let currentSurah = 1;

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

        localStorage.setItem('language', lang); // Save preference
    }

    languageButtons.forEach(button => {
        button.addEventListener('click', () => {
            switchLanguage(button.dataset.lang);
        });
    });

    // --- API Functions ---
    async function fetchQuranData(surahNumber) {
        try {
            const url = `https://api.quran.com/api/v4/verses/by_chapter/${surahNumber}?language=en&words=true&translations=131`;
            const response = await fetch(url);

            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }

            const data = await response.json();

            if (!data ||!data.verses) {
                throw new Error("Invalid data received from API (no verses).");
            }

            return data.verses;

        } catch (error) {
            console.error("Error fetching Quran data:", error);
            quranText.textContent = `Failed to load Quran data: ${error.message}`;
            return;
        }
    }

    async function fetchSurahList() {
        try {
            const response = await fetch('https://api.quran.com/api/v4/chapters?language=en');

            if (!response.ok) {
                throw new Error(`HTTP error fetching Surah list! Status: ${response.status}`);
            }

            const data = await response.json();

            if (!data ||!data.chapters) {
                throw new Error("Invalid data received from API (no chapters).");
            }
            return data.chapters;

        } catch (error) {
            console.error("Error fetching Surah list:", error);
            quranText.textContent = `Failed to load Surah list: ${error.message}`;
            return;
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
            currentSurah = surahNumber; // Update currentSurah
        } else {
            quranText.textContent = "No verses found for this Surah."; // Handle empty Surahs
        }
    }

    // --- Event Listeners ---

    if (surahSelect) {
        surahSelect.addEventListener('change', (event) => {
            displayFullSurah(parseInt(event.target.value, 10));
        });
    }

    // Add event listeners to all 'prev-surah-btn-en' buttons
    prevSurahBtnEn.forEach(btn => {
        btn.addEventListener('click', () => {
            if (currentSurah > 1) {
                currentSurah--;
                surahSelect.value = currentSurah;
                surahSelect.dispatchEvent(new Event('change'));
            }
        });
    });

    // Add event listeners to all 'prev-surah-btn-ar' buttons
    prevSurahBtnAr.forEach(btn => {
        btn.addEventListener('click', () => {
            if (currentSurah > 1) {
                currentSurah--;
                surahSelect.value = currentSurah;
                surahSelect.dispatchEvent(new Event('change'));
            }
        });
    });

    // Add event listeners to all 'next-surah-btn-en' buttons
    nextSurahBtnEn.forEach(btn => {
        btn.addEventListener('click', () => {
            if (currentSurah < 114) {
                currentSurah++;
                surahSelect.value = currentSurah;
                surahSelect.dispatchEvent(new Event('change'));
            }
        });
    });

    // Add event listeners to all 'next-surah-btn-ar' buttons
    nextSurahBtnAr.forEach(btn => {
        btn.addEventListener('click', () => {
            if (currentSurah < 114) {
                currentSurah++;
                surahSelect.value = currentSurah;
                surahSelect.dispatchEvent(new Event('change'));
            }
        });
    });

    // --- Initial Setup ---
    populateSurahSelect();

    // Load saved language preference
    const savedLanguage = localStorage.getItem('language');
    if (savedLanguage) {
        switchLanguage(savedLanguage);
    } else {
        switchLanguage('en'); // Default to English
    }

    document.getElementById('loading-indicator').style.display = 'none'; // Hide loading indicator

});