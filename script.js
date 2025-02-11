const surahSelect = document.getElementById('surah-select');
const quranText = document.getElementById('quran-text');
const prevSurahBtn = document.getElementById('prev-surah');
const nextSurahBtn = document.getElementById('next-surah');
const languageButtons = document.querySelectorAll('.lang-btn');

let currentSurah = 1;

// --- Language Switching (No changes here) ---
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
}

languageButtons.forEach(button => {
    button.addEventListener('click', () => {
        switchLanguage(button.dataset.lang);
    });
});

// --- Quran API Functions ---
async function fetchQuranData(surahNumber) {
    try {
        const url = `https://api.quran.com/api/v4/verses/by_chapter/${surahNumber}?language=en&words=true&translations=131`;
        console.log("Fetching URL:", url); // Log the URL being fetched

        const response = await fetch(url);
        console.log("API Response:", response); // Log the *entire* response object

        const data = await response.json();
        console.log("API Data:", data); // Log the parsed JSON data

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        if (!data || !data.verses) {
            throw new Error("Invalid data received from API.");
        }

        return data.verses;

    } catch (error) {
        console.error("Error fetching Quran data:", error);
        quranText.textContent = `Failed to load Quran data. Error: ${error.message}`;
        return [];
    }
}
async function fetchSurahList() {
    try {
        const response = await fetch('https://api.quran.com/api/v4/chapters?language=en');
        const data = await response.json();

        if (!response.ok) {
            throw new Error(`HTTP error fetching Surah list! status: ${response.status}`);
        }
		if (!data || !data.chapters) {
            throw new Error("Invalid Surah list data received from API.");
        }
        return data.chapters;

    } catch (error) {
        console.error("Error fetching Surah list:", error);
        quranText.innerHTML = `<p>Error loading Surah list: ${error.message}</p>`;
        return []; // Return empty array on error
    }
}

async function populateSurahSelect() {
    const surahs = await fetchSurahList();
    if (surahs && surahs.length > 0) { //check surahs is valid
        surahs.forEach(surah => {
            const option = document.createElement('option');
            option.value = surah.id;
            option.textContent = `${surah.id}. ${surah.name_simple} (${surah.name_arabic})`;
            surahSelect.appendChild(option);
        });
        surahSelect.dispatchEvent(new Event('change'));
    } else {
         quranText.innerHTML = "<p>No Surahs were found.</p>"; //Handle no surahs
    }
}

async function displayFullSurah(surahNumber) {
    const verses = await fetchQuranData(surahNumber);

    if (verses && verses.length > 0) {
        let html = '';
        verses.forEach(verse => {
            html += `<p>`;

            // --- MORE ROBUST WORD HANDLING ---
            if (verse.words && Array.isArray(verse.words)) {
                verse.words.forEach(word => {
                    const wordText = word.text || word.transliteration?.text || word.code || word.word || "???";
                    const translationText = word.translation?.text || "";
                    html += `<span title="${translationText}">${wordText} </span>`;
                });
            } else {
                console.warn("Verse is missing 'words' array:", verse);
                html += `<span>[Missing Word Data]</span>`;
            }

            html += ` (${verse.verse_key})</p>`;
        });
        quranText.innerHTML = html;
        currentSurah = surahNumber;
    }
}

// Event Listeners for Quran Navigation (No changes)
surahSelect.addEventListener('change', (event) => {
    displayFullSurah(parseInt(event.target.value));
});

prevSurahBtn.addEventListener('click', () => {
    if (currentSurah > 1) {
        surahSelect.value = currentSurah - 1;
        surahSelect.dispatchEvent(new Event('change'));
    }
});

nextSurahBtn.addEventListener('click', () => {
    if (currentSurah < 114) {
        surahSelect.value = currentSurah + 1;
        surahSelect.dispatchEvent(new Event('change'));
    }
});

populateSurahSelect();
const savedLanguage = localStorage.getItem('language');
if (savedLanguage) {
    switchLanguage(savedLanguage);
} else {
    switchLanguage('en');
}