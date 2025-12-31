export class CommandRegistry {
	constructor() {
		this.commands = new Map();
	}

	registerCommand(id, handler) {
		if (this.commands.has(id)) {
			console.warn(`Command ${id} is already registered.`);
			return;
		}
		this.commands.set(id, handler);
		return {
			dispose: () => this.commands.delete(id),
		};
	}

	executeCommand(id, ...args) {
		const handler = this.commands.get(id);
		if (!handler) {
			console.error(`Command ${id} not found.`);
			return Promise.reject(new Error(`Command ${id} not found`));
		}
		try {
			return Promise.resolve(handler(...args));
		} catch (error) {
			console.error(`Error executing command ${id}:`, error);
			return Promise.reject(error);
		}
	}

	getCommands() {
		return Array.from(this.commands.keys());
	}
}

export const commandRegistry = new CommandRegistry();
