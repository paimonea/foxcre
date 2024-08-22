const toggleButton = document.querySelector('#theme-toggle');
const svgLight = document.querySelector('#svg-light');
const svgDark = document.querySelector('#svg-dark');

/**
 * On Page Load Set The Theme From LocalStorage
 */
if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
  svgDark.classList.add('hidden');
  svgLight.classList.remove('hidden');
  document.documentElement.classList.add('dark')
  localStorage.setItem('theme', 'dark');
} else {
  svgDark.classList.remove('hidden');
  svgLight.classList.add('hidden');
  document.documentElement.classList.remove('dark')
  localStorage.setItem('theme', 'light');
}

/**
 * Toggle Theme When User Click On The Button
 */
toggleButton.addEventListener('click', () => {
  if (localStorage.theme === 'dark') {
    svgDark.classList.remove('hidden');
    svgLight.classList.add('hidden');
    document.documentElement.classList.remove('dark');
    localStorage.setItem('theme', 'light');
  } else {
    svgDark.classList.add('hidden');
    svgLight.classList.remove('hidden');
    document.documentElement.classList.add('dark');
    localStorage.setItem('theme', 'dark');
  }
})


