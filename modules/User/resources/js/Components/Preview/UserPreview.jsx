import { Link } from "@inertiajs/react";
import { UserInfo } from "@/Components/Content/Shared/UserInfo";

export function UserPreview({ name, username, avatar }) {
	return (
		<Link
			href={route("user.view", {
				username: username,
			})}
		>
			<div
				key={username}
				className="rounded-lg hover:bg-accent/50 cursor-pointer transition-colors"
			>
				<UserInfo name={name} username={username} avatar={avatar} />
			</div>
		</Link>
	);
}
