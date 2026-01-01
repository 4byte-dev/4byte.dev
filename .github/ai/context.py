import subprocess

MAX_FILES = 8
MAX_LINES = 300

def collect_context():
    files = subprocess.check_output(
        ["git", "ls-files"]
    ).decode().splitlines()

    context = []
    for f in files[:MAX_FILES]:
        with open(f, errors="ignore") as fp:
            lines = fp.readlines()[:MAX_LINES]
            context.append(f"\n# File: {f}\n{''.join(lines)}")

    return "\n".join(context)
