import 'dotenv/config';
import { Client, Events, GatewayIntentBits, REST, Routes, SlashCommandBuilder } from 'discord.js';
import { getCommands, runCommand, getPlayerByDiscordId } from './api.js';

const token = process.env.DISCORD_BOT_TOKEN;
const guildId = process.env.DISCORD_GUILD_ID || null;

if (!token) {
  console.error('DISCORD_BOT_TOKEN fehlt in .env');
  process.exit(1);
}

const client = new Client({ intents: [GatewayIntentBits.Guilds] });

const rest = new REST().setToken(token);

async function registerSlashCommands() {
  const commands = [
    new SlashCommandBuilder()
      .setName('befehl')
      .setDescription('Quick-Befehl ausführen oder auflisten (gespeicherte Befehle von der Website)')
      .addSubcommand(sub =>
        sub
          .setName('run')
          .setDescription('Einen gespeicherten Befehl ausführen')
          .addStringOption(opt =>
            opt.setName('name').setDescription('Name des Befehls (z. B. spitzhacke)').setRequired(true)
          )
      )
      .addSubcommand(sub =>
        sub.setName('list').setDescription('Alle verfügbaren Quick-Befehle anzeigen')
      )
      .toJSON(),
    new SlashCommandBuilder()
      .setName('spieler')
      .setDescription('Zeigt deinen verknüpften Spieler/User von der Website an (wenn verknüpft)')
      .toJSON(),
  ];

  try {
    if (guildId) {
      await rest.put(Routes.applicationGuildCommands(client.user.id, guildId), { body: commands });
      console.log('Slash-Commands für Guild registriert:', guildId);
    } else {
      await rest.put(Routes.applicationCommands(client.user.id), { body: commands });
      console.log('Slash-Commands global registriert');
    }
  } catch (e) {
    console.error('Registrierung fehlgeschlagen:', e);
  }
}

client.once(Events.ClientReady, async () => {
  console.log('Bot eingeloggt als', client.user.tag);
  await registerSlashCommands();
});

client.on(Events.InteractionCreate, async (interaction) => {
  if (!interaction.isChatInputCommand()) return;

  if (interaction.commandName === 'befehl') {
    const sub = interaction.options.getSubcommand();

    if (sub === 'run') {
      const name = interaction.options.getString('name', true).trim().toLowerCase().replace(/\s+/g, '_');
      await interaction.deferReply();

      try {
        const result = await runCommand(name);
        if (result.notFound) {
          await interaction.editReply({ content: '❌ Befehl „' + name + '“ nicht gefunden. Nutze `/befehl list` für alle Befehle.' });
          return;
        }
        await interaction.editReply({ content: result.response_text || '(Keine Antwort)' });
      } catch (e) {
        console.error(e);
        await interaction.editReply({ content: '⚠️ Verbindung zur Website fehlgeschlagen. Bitte später erneut versuchen.' });
      }
      return;
    }

    if (sub === 'list') {
      await interaction.deferReply({ ephemeral: true });

      try {
        const list = await getCommands();
        if (!list.length) {
          await interaction.editReply({ content: 'Es sind noch keine Quick-Befehle angelegt. Admin kann sie auf der Website anlegen.' });
          return;
        }
        const lines = list.map(c => `• **/${c.name}** – ${(c.description || '').slice(0, 50)}`);
        await interaction.editReply({
          content: '**Verfügbare Quick-Befehle** (mit `/befehl run name:<name>` ausführen):\n' + lines.join('\n'),
          ephemeral: true,
        });
      } catch (e) {
        console.error(e);
        await interaction.editReply({ content: '⚠️ Verbindung zur Website fehlgeschlagen.', ephemeral: true });
      }
      return;
    }
  }

  if (interaction.commandName === 'spieler') {
    await interaction.deferReply({ ephemeral: true });
    try {
      const data = await getPlayerByDiscordId(interaction.user.id);
      if (!data.found || !data.user) {
        await interaction.editReply({
          content: 'Du bist noch nicht mit einem Spieler/User der Website verknüpft. Verknüpfung erfolgt im UserCP auf der Website.',
          ephemeral: true,
        });
        return;
      }
      await interaction.editReply({
        content: `**Verknüpfter Spieler:** ${data.user.name}${data.user.discord_handle ? ' | Discord: ' + data.user.discord_handle : ''}`,
        ephemeral: true,
      });
    } catch (e) {
      console.error(e);
      await interaction.editReply({ content: '⚠️ Verbindung zur Website fehlgeschlagen.', ephemeral: true });
    }
  }
});

client.login(token).catch((e) => {
  console.error('Login fehlgeschlagen:', e);
  process.exit(1);
});
