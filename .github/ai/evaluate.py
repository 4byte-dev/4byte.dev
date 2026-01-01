CONFIDENCE_THRESHOLD = 0.75

def should_apply_patch(data):
    return (
        data["confidence"] >= CONFIDENCE_THRESHOLD
        and len(data.get("files", [])) <= 3
        and data.get("patch")
    )
