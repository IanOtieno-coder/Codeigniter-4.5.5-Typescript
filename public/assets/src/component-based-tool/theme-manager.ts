export class ThemeManager {
    static setTheme(theme: string) {
        $(document.documentElement).data('theme', theme);
    }

    static toggleDarkMode() {
        const theme = $(document.documentElement).data('theme') === 'dark' ? 'light' : 'dark';
        ThemeManager.setTheme(theme);
    }
}
