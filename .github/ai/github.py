import requests, os

def comment_issue(body):
    repo = os.environ["REPO"]
    issue = os.environ["ISSUE_NUMBER"]

    url = f"https://api.github.com/repos/{repo}/issues/{issue}/comments"

    requests.post(
        url,
        headers={
            "Authorization": f"Bearer {os.environ['GITHUB_TOKEN']}"
        },
        json={"body": f"🤖 AI Analysis:\n\n{body}"}
    )
