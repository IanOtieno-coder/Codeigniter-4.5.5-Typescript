const translations: { [key: string] : {} } =  {
    en: [{ welcome: 'Welcome' }],
    fr: [{ welcome: 'Bienvenue' }],
};

export class i18n {
    static currentLanguage = 'en';

    static translate(key: string) {
        return translations[i18n.currentLanguage] || key;
    }

    static changeLanguage(language: string) {
        i18n.currentLanguage = language;
    }
}

/* 

// Usage in a component
render() {
    return this.html`<p>${i18n.translate('welcome')}</p>`;
}

*/
