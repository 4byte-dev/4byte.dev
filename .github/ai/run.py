import os, json
from context import collect_context
from evaluate import should_apply_patch
from github import comment_issue
from mini_swe_agent import run_agent

context = collect_context()

prompt = open(".github/ai/prompt.txt").read()
prompt = prompt.replace("{{ISSUE_TITLE}}", os.environ["ISSUE_TITLE"])
prompt = prompt.replace("{{ISSUE_BODY}}", os.environ["ISSUE_BODY"])
prompt = prompt.replace("{{CODE_CONTEXT}}", context)

result = run_agent(prompt)
data = json.loads(result)

if should_apply_patch(data):
    with open("patch.diff", "w") as f:
        f.write(data["patch"])
else:
    comment_issue(data["analysis"])
