import DOMPurify from "dompurify";

export const sanitize = (html) => DOMPurify.sanitize(html);
