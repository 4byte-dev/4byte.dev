import { useEffect, useState } from 'react';
import { useEditorStore } from '../../Stores/EditorStore';

export function usePreviewRunner() {
    const { files, addConsoleLog } = useEditorStore();
    const [iframeSrc, setIframeSrc] = useState("");

    useEffect(() => {
        const handler = (e) => {
            if (e.data.type === "console") {
                addConsoleLog(e.data);
            }
        };
        window.addEventListener("message", handler);
        return () => window.removeEventListener("message", handler);
    }, [addConsoleLog]);

    useEffect(() => {
        const entry = files["index.html"];
        if (!entry) return;
        let html = entry.content;

        html = html.replace(/<link[^>]+href=["'](.*?)["'][^>]*>/g, (match, path) => {
            const file = files[path.replace(/^\.\//, "")];
            return file ? `<style>${file.content}</style>` : match;
        });

        const processJS = (content) =>
            content.replace(/from\s+['"](.*?)['"]/g, (_, p) => {
                const key = Object.keys(files).find((k) => k.endsWith(p.replace(/^\.\//, "")));
                if (key) {
                    return `from "${URL.createObjectURL(new Blob([files[key].content], { type: "application/javascript" }))}"`;
                }
                return `from "${p}"`;
            });

        html = html.replace(/<script[^>]+src=["'](.*?)["'][^>]*><\/script>/g, (match, p) => {
            const key = p.replace(/^\.\//, "");
            const file = files[key];
            if (!file) return match;

            if (key.endsWith(".jsx")) {
                return `<script type="text/babel" data-type="module">${processJS(file.content)}</script>`;
            }

            return `<script type="module">${processJS(file.content)}</script>`;
        });

        const proxy = `<script>
    const _log = console.log;
    console.log = (...args) => { window.parent.postMessage({type:'console', message: args, level: 'log', timestamp: Date.now()}, '*'); _log(...args); }
    const _error = console.error;
    console.error = (...args) => { window.parent.postMessage({type:'console', message: args, level: 'error', timestamp: Date.now()}, '*'); _error(...args); }
    const _warn = console.warn;
    console.warn = (...args) => { window.parent.postMessage({type:'console', message: args, level: 'warn', timestamp: Date.now()}, '*'); _warn(...args); }
    const _info = console.info;
    console.info = (...args) => { window.parent.postMessage({type:'console', message: args, level: 'info', timestamp: Date.now()}, '*'); _info(...args); }
    
    window.onerror = (msg, url, line) => { window.parent.postMessage({type:'console', message: ['Error: ' + msg], level: 'error', timestamp: Date.now()}, '*'); };
</script>`;
        setIframeSrc(html + proxy);
    }, [files]);

    return iframeSrc;
}
