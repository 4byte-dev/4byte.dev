export const ENDPOINT = "https://your-api-endpoint.com/api/project"; // User can configure this

// Mock In-Memory Database for demonstration
let MOCK_DB = {
	1: { id: "1", name: "My React App", updatedAt: new Date().toISOString() },
	2: { id: "2", name: "Landing Page", updatedAt: new Date(Date.now() - 86400000).toISOString() },
};

export const PersistenceService = {
	saveProject: async (files, name = "Untitled Project") => {
		console.log("Saving project to", ENDPOINT, { name, files });
		try {
			// Mock API call
			await new Promise((resolve) => setTimeout(resolve, 500));

			// Mock DB Update
			const id = Date.now().toString();
			MOCK_DB[id] = { id, name, updatedAt: new Date().toISOString() };

			return { success: true, message: `Project "${name}" saved!`, id };
		} catch (error) {
			console.error("Failed to save project:", error);
			throw error;
		}
	},

	loadProject: async (projectId) => {
		console.log("Loading project", projectId, "from", ENDPOINT);
		try {
			// Artificial delay
			await new Promise((resolve) => setTimeout(resolve, 500));

			// Return null or mock data.
			// In a real app, we'd fetch specific files for this project ID.
			// For now, allow returning null to signal "no data" or just simulated success.
			const project = MOCK_DB[projectId];
			if (!project) return null;

			return {
				id: projectId,
				name: project.name,
				files: null, // We aren't actually storing files in MOCK_DB to save memory/complexity here, but normally would.
			};
		} catch (error) {
			console.error("Failed to load project:", error);
			throw error;
		}
	},

	listProjects: async () => {
		console.log("Fetching project list from", ENDPOINT);
		try {
			await new Promise((resolve) => setTimeout(resolve, 500));
			return Object.values(MOCK_DB).sort(
				(a, b) => new Date(b.updatedAt) - new Date(a.updatedAt),
			);
		} catch (error) {
			console.error("Failed to list projects", error);
			throw error;
		}
	},
};
