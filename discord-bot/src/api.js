import 'dotenv/config';

const API_URL = (process.env.LARAVEL_API_URL || '').replace(/\/$/, '');
const API_KEY = process.env.DISCORD_BOT_API_KEY || '';

function headers() {
  return {
    'Content-Type': 'application/json',
    'X-API-Key': API_KEY,
    'Accept': 'application/json',
  };
}

export async function getCommands() {
  const res = await fetch(`${API_URL}/api/discord-bot/commands`, { headers: headers() });
  if (!res.ok) throw new Error(`API ${res.status}: ${await res.text()}`);
  const data = await res.json();
  return data.commands || [];
}

export async function runCommand(name) {
  const res = await fetch(`${API_URL}/api/discord-bot/commands/run`, {
    method: 'POST',
    headers: headers(),
    body: JSON.stringify({ name }),
  });
  if (res.status === 404) {
    const d = await res.json();
    return { notFound: true, response: d.response_text };
  }
  if (!res.ok) throw new Error(`API ${res.status}: ${await res.text()}`);
  return await res.json();
}

export async function getPlayerByDiscordId(discordId) {
  const res = await fetch(`${API_URL}/api/discord-bot/player?discord_id=${encodeURIComponent(discordId)}`, {
    headers: headers(),
  });
  if (!res.ok) throw new Error(`API ${res.status}`);
  return await res.json();
}

export async function linkDiscord(discordId, userId) {
  const res = await fetch(`${API_URL}/api/discord-bot/link`, {
    method: 'POST',
    headers: headers(),
    body: JSON.stringify({ discord_id: discordId, user_id: userId }),
  });
  if (!res.ok) throw new Error(`API ${res.status}: ${await res.text()}`);
  return await res.json();
}
