import {
	File,
	FileCode,
	FileJson,
	FileType,
	FileText,
	Coffee,
	Braces,
	Globe,
	Package,
	Settings,
	Shield,
	Zap,
} from "lucide-react";
import React from "react";

const FILE_TYPES_MAP = {
	html: { icon: FileCode, color: "text-orange-500", label: "HTML" },
	xml: { icon: FileCode, color: "text-orange-500", label: "XML" },
	css: { icon: FileType, color: "text-blue-400", label: "CSS" },
	scss: { icon: FileType, color: "text-pink-400", label: "SCSS" },
	sass: { icon: FileType, color: "text-pink-400", label: "SASS" },
	less: { icon: FileType, color: "text-blue-600", label: "LESS" },

	js: { icon: FileJson, color: "text-yellow-400", label: "JavaScript" },
	jsx: { icon: FileJson, color: "text-yellow-400", label: "JavaScript React" },
	ts: { icon: FileJson, color: "text-blue-500", label: "TypeScript" },
	tsx: { icon: FileJson, color: "text-blue-500", label: "TypeScript React" },
	json: { icon: Braces, color: "text-yellow-500", label: "JSON" },

	php: { icon: Globe, color: "text-purple-400", label: "PHP" },
	py: { icon: FileCode, color: "text-blue-500", label: "Python" },
	go: { icon: Zap, color: "text-cyan-400", label: "Go" },
	rs: { icon: Settings, color: "text-orange-600", label: "Rust" },
	java: { icon: Coffee, color: "text-red-500", label: "Java" },
	c: { icon: FileCode, color: "text-blue-600", label: "C" },
	cpp: { icon: FileCode, color: "text-blue-600", label: "C++" },
	h: { icon: FileCode, color: "text-blue-600", label: "C Header" },

	md: { icon: FileText, color: "text-gray-300", label: "Markdown" },
	txt: { icon: FileText, color: "text-gray-400", label: "Plain Text" },

	default: { icon: File, color: "text-gray-400", label: "Plain Text" },
};

const FILENAME_MAP = {
	"package.json": {
		icon: Package,
		color: "text-red-400",
		label: "Node Package",
	},
	"package-lock.json": {
		icon: Shield,
		color: "text-yellow-500",
		label: "Package Lock",
	},
	"readme.md": {
		icon: FileText,
		color: "text-blue-200",
		label: "Markdown",
	},
	".env": {
		icon: Settings,
		color: "text-yellow-600",
		label: "Environment",
	},
};

export const getFileIcon = (filename) => {
	const lowerName = filename.toLowerCase();

	if (FILENAME_MAP[lowerName]) {
		const { icon: Icon, color } = FILENAME_MAP[lowerName];
		return <Icon size={14} className={color} />;
	}

	const parts = lowerName.split(".");
	if (parts.length > 1) {
		const ext = parts.pop();
		if (FILE_TYPES_MAP[ext]) {
			const { icon: Icon, color } = FILE_TYPES_MAP[ext];
			return <Icon size={14} className={color} />;
		}
	}

	const { icon: Icon, color } = FILE_TYPES_MAP.default;
	return <Icon size={14} className={color} />;
};

export const getFileLabel = (filename) => {
	const lowerName = filename.toLowerCase();

	if (FILENAME_MAP[lowerName]) {
		const { label } = FILENAME_MAP[lowerName];
		return label;
	}

	const parts = lowerName.split(".");
	if (parts.length > 1) {
		const ext = parts.pop();
		if (FILE_TYPES_MAP[ext]) {
			const { label } = FILE_TYPES_MAP[ext];
			return label;
		}
	}

	const { label } = FILE_TYPES_MAP.default;
	return label;
};
