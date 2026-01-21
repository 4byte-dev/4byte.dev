import i18n from "i18next";
import { initReactI18next } from "react-i18next";

const globalLangs = import.meta.glob("/resources/js/Lang/*.json", { eager: true });

const moduleLangs = import.meta.glob("/modules/**/resources/js/Lang/*.json", { eager: true });

function buildResources(files) {
	const result = {};

	for (const path in files) {
		const langMatch = path.match(/\/([^/]+)\.json$/);
		if (!langMatch) continue;

		const lang = langMatch[1];
		result[lang] ??= {};

		const data = files[path].default ?? files[path];

		if (data.default) {
			Object.assign(result[lang], data.default, data);
			delete result[lang].default;
		} else {
			Object.assign(result[lang], data);
		}
	}

	return result;
}

const globalResources = buildResources(globalLangs);
const moduleResources = buildResources(moduleLangs);

const resources = {};

for (const lang of new Set([...Object.keys(globalResources), ...Object.keys(moduleResources)])) {
	resources[lang] = {
		translation: {
			...(globalResources[lang] ?? {}),
			...(moduleResources[lang] ?? {}),
		},
	};
}

function getInitialLng() {
	if (typeof window === "undefined") return "tr";

	return localStorage.getItem("language") || document.documentElement.lang || "tr";
}

if (!i18n.isInitialized) {
	i18n.use(initReactI18next).init({
		resources,
		lng: getInitialLng(),
		fallbackLng: "tr",
		interpolation: { escapeValue: false },
	});
}

export default i18n;
