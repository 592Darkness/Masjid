document.addEventListener('DOMContentLoaded', () => {
    // ... (your existing variables: surahSelect, quranText, etc.) ...
    const surahSelect = document.getElementById('surah-select');
    const quranText = document.getElementById('quran-text');
    const prevSurahBtn = document.getElementById('prev-surah-bten');
    const nextSurahBtn = document.getElementById('next-surah-bten');
    const languageButtons = document.querySelectorAll('.lang-btn');
    const loadingIndicator = document.getElementById('loading-indicator');
    const navToggle = document.getElementById('nav-toggle');
    const mainNav = document.getElementById('main-nav');
    const themeToggleButton = document.getElementById('theme-toggle'); // Get the new button

    let currentSurah = 1;
    let currentVerses = []; // Keep this for consistency


    // --- Theme Switching (NEW) ---
     function applyThemePreference() {
        const savedTheme = localStorage.getItem('theme') || 'light';
        setTheme(savedTheme);
    }
    function setTheme(theme) {
        if (theme === 'dark') {
            document.body.classList.add('dark-theme');
            themeToggleButton.classList.add('dark-mode'); // Add class for icons.
            localStorage.setItem('theme', 'dark');

        } else {
            document.body.classList.remove('dark-theme');
            themeToggleButton.classList.remove('dark-mode'); // Remove class
            localStorage.setItem('theme', 'light');
        }

           //Make surah to load when change occurs.
        if (surahSelect) {  //Check surahSelect exisits- only on quran page
              displaySurah(currentSurah);
          }
    }


      // Add Event listner: Theme Toggle.
       if (themeToggleButton) { //Ensure theme toggle button exists before attaching events
          themeToggleButton.addEventListener('click', () => {
           const currentTheme = localStorage.getItem('theme') || 'light';
           const newTheme = currentTheme === 'light' ? 'dark' : 'light';
           setTheme(newTheme);
          });
        }

      // --- Navigation Toggle and Auto-Hide --- (No Changes)
      navToggle.addEventListener('click', (event) => {
          mainNav.classList.toggle('open');
          event.stopPropagation();
      });

      document.addEventListener('click', (event) => {
          if (!mainNav.contains(event.target) && !navToggle.contains(event.target)) {
              mainNav.classList.remove('open');
          }
      });

      mainNav.addEventListener('mouseleave', () => {
          mainNav.classList.remove('open');
      });

        // --- Language Switching ---

      function switchLanguage(lang) {
        // ..(The rest of langauge swicth code)
           const arElements = document.querySelectorAll('.ar');
          const enElements = document.querySelectorAll('.en');

          if (lang === 'ar') {
              arElements.forEach(el => {
                  el.style.display = 'block';
                  el.classList.remove('opacity');
              });
              enElements.forEach(el => el.style.display = 'none');
              document.documentElement.setAttribute('lang', 'ar');
              document.documentElement.setAttribute('dir', 'rtl');
          } else {
              arElements.forEach(el => el.style.display = 'none');
              enElements.forEach(el => {
                  el.style.display = 'block';
                  el.classList.remove('opacity');
              });
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
          if (surahSelect) {  //Check surahSelect exisits- only on quran page
              displaySurah(currentSurah);
          }
      }

          function applyLanguagePreference() {
        // ... (Your existing language preference code)
          const savedLanguage = localStorage.getItem('language') || 'en';
          switchLanguage(savedLanguage);
      }


    languageButtons.forEach(button => {
        button.addEventListener('click', () => {
            switchLanguage(button.dataset.lang);
        });
    });




      // --- API Functions (Modified for Pagination) ---
    async function fetchQuranData(surahNumber, page = 1, perPage = 20) {
         // ... (Your existing API code with comments and error checking)...
       loadingIndicator.style.display = 'block';
     const language = localStorage.getItem('language') || 'en';//Get the selected language
          //get selected them
     const theme = localStorage.getItem('theme') || 'light';

        //Translations ids
        // Translation IDs (Adjust as necessary)
        const translationId = language === 'ar' ? '33' : '131';

     try {
          const url = `https://api.quran.com/api/v4/verses/by_chapter/${surahNumber}?language=${language}&words=true&translations=${translationId}&page=${page}&per_page=${perPage}`; //Add page and per-page

          const response = await fetch(url);

          if (!response.ok) {
              throw new Error(`HTTP error! Status: ${response.status}`);
          }

          const data = await response.json();
          const verses = data.verses;
          if (!verses || verses.length === 0) {
              throw new Error("Invalid data received from API (no verses).");
          }

         return {
            verses: verses,
            pagination: data.pagination
        };

      } catch (error) {
          console.error("Error fetching Quran data:", error);
          //Provide user with feedback on failure- not just in the dev.
        quranText.innerHTML = `<p class = "en">Failed to load Quran data: ${error.message}</p><p class="ar">تعذر جلب بيانات القران الكريم: ${error.message}</p>`;

          return { verses: [], pagination: {} };
      } finally {
          loadingIndicator.style.display = 'none';
      }
    }

    async function fetchSurahList() {
        // ...(Your existing surah fetching, including comments/error handling)
          try {
              const response = await fetch('https://api.quran.com/api/v4/chapters?language=en');
              if (!response.ok) {
                  throw new Error(`HTTP error! Status: ${response.status}`);
              }
              const data = await response.json();
              if (!data || !data.chapters) {
                  throw new Error("Invalid data received from API (no chapters).");
              }
              return data.chapters;
          } catch (error) {
              console.error("Error fetching Surah list:", error);
              quranText.innerHTML = `<p>Failed to load Surah list: ${error.message}</p>`;
              return []; // Return empty array on error
          }
    }


    async function populateSurahSelect() {
        // ... (Your existing population of Surah list)
         const surahs = await fetchSurahList();
          if (surahs.length > 0) {
              surahs.forEach(surah => {
                  const option = document.createElement('option');
                  option.value = surah.id;
                  option.textContent = `${surah.id}. ${surah.name_simple} (${surah.name_arabic})`;
                  surahSelect.appendChild(option);
              });
              surahSelect.value = currentSurah;//Default value.
              surahSelect.dispatchEvent(new Event('change'));//Trigger surah event change
          } else {
              quranText.textContent = "No Surahs found.";
          }
    }

    // --- Display Functions (Modified to remove '?') ---
  async function displaySurah(surahNumber) {
     // ..(Rest of the display surah)..
       currentSurah = surahNumber;
      quranText.innerHTML = ''; // Clear previous content

      let currentPage = 1;
      let totalPages = 1;

      do {
          const result = await fetchQuranData(surahNumber, currentPage);
          const verses = result.verses;
            totalPages = result.pagination.total_pages;

          if (verses.length > 0) {
              verses.forEach(verse => {
                  let html = `<p data-ayah="${verse.verse_number}" dir="${document.documentElement.dir === 'rtl' ? 'rtl' : 'ltr'}">`;

                  if (verse.words && Array.isArray(verse.words)) {
                      verse.words.forEach(word => {
                        //  let wordText = word.text_uthmani || word.transliteration?.text || word.code || "???";
                          let wordText = word.text_uthmani || word.transliteration?.text || word.code || ""; // KEY CHANGE

                          // Remove any '?' from the word text.  Most robust approach.
                          wordText = wordText.replace(/\?/g, ''); // Regular expression to remove all '?'

                          const translationText = word.translation?.text || "";
                          html += `<span data-word-id="${word.id}" title="${translationText}">${wordText}</span> `;

                      });
                  } else {
                    console.warn("Verse is missing 'words' array:", verse);
                      html += '<span>[Missing Word Data]</span>';
                  }
                  html += ` (${verse.verse_key})</p>`;
                  quranText.innerHTML += html;
              });
          }

          currentPage++;

      } while (currentPage <= totalPages);
  }

  // --- Event Listeners ---
    surahSelect.addEventListener('change', (event) => {
      // ...(Rest of surah, next surah and Prev surah)..
     displaySurah(parseInt(event.target.value, 10));
  });

  prevSurahBtn.addEventListener('click', () => {
      if (currentSurah > 1) {
          currentSurah--;
          surahSelect.value = currentSurah;
          surahSelect.dispatchEvent(new Event('change')); //  Important
      }
  });

  nextSurahBtn.addEventListener('click', () => {
      if (currentSurah < 114) {
          currentSurah++;
          surahSelect.value = currentSurah;
          surahSelect.dispatchEvent(new Event('change')); // Important
      }
  });


    quranText.addEventListener('click', (event) => {
       // ...(Rest click and quran translation)
      if (event.target.tagName === 'SPAN' && event.target.hasAttribute('data-word-id')) {
          const wordId = event.target.dataset.wordId;
         //Combine with current verse
          const word = currentVerses.flatMap(v => v.words || []).find(w => w.id == wordId);

          if (word) {
              alert(`Translation: ${word.translation.text}`);
          }
      }
  });

  
      // --- Initial Setup ---

     if (surahSelect) { // Check if surahSelect exists before using
      populateSurahSelect();
    }
        applyLanguagePreference();
        applyThemePreference(); // Apply saved theme on page load

});