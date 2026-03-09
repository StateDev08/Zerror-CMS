#!/usr/bin/env node
/**
 * Einmalig Slash-Commands bei Discord registrieren (ohne Bot dauerhaft laufen zu lassen).
 * Nutzung: DISCORD_BOT_TOKEN=xxx DISCORD_GUILD_ID=optional node src/register-commands.js
 */
import 'dotenv/config';
import { REST, Routes } from 'discord.js';

const token = process.env.DISCORD_BOT_TOKEN;
const guildId = process.env.DISCORD_GUILD_ID || null;
const applicationId = process.env.DISCORD_APPLICATION_ID || null;

if (!token) {
  console.error('DISCORD_BOT_TOKEN fehlt');
  process.exit(1);
}

const rest = new REST().setToken(token);

const commands = [
  {
    name: 'befehl',
    description: 'Quick-Befehl ausführen oder auflisten (gespeicherte Befehle von der Website)',
    options: [
      {
        type: 2, // SUB_COMMAND
        name: 'run',
        description: 'Einen gespeicherten Befehl ausführen',
        options: [{ type: 3, name: 'name', description: 'Name des Befehls (z. B. spitzhacke)', required: true }],
      },
      { type: 2, name: 'list', description: 'Alle verfügbaren Quick-Befehle anzeigen' },
    ],
  },
  {
    name: 'spieler',
    description: 'Zeigt deinen verknüpften Spieler/User von der Website an (wenn verknüpft)',
  },
];

async function main() {
  const appId = applicationId || (await rest.get(Routes.oauth2CurrentApplication()).then((a) => a.id));
  if (guildId) {
    await rest.put(Routes.applicationGuildCommands(appId, guildId), { body: commands });
    console.log('Guild-Commands registriert für', guildId);
  } else {
    await rest.put(Routes.applicationCommands(appId), { body: commands });
    console.log('Global Commands registriert');
  }
}

main().catch((e) => {
  console.error(e);
  process.exit(1);
});
