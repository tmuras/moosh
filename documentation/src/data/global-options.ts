export interface GlobalOption {
  name: string;
  shortcut?: string;
  type: 'value_required' | 'value_none';
  description: string;
  default?: string;
}

export const globalOptions: GlobalOption[] = [
  {
    name: '--moodle-path',
    shortcut: '-p',
    type: 'value_required',
    description: 'Path to Moodle installation directory. If not provided, moosh will walk up the directory tree to find a Moodle installation.',
  },
  {
    name: '--user',
    shortcut: '-u',
    type: 'value_required',
    description: 'Moodle username to log in as when bootstrapping.',
    default: 'admin',
  },
  {
    name: '--no-login',
    shortcut: '-l',
    type: 'value_none',
    description: 'Skip user login during Moodle bootstrap. Useful for commands that do not require an authenticated user.',
  },
  {
    name: '--no-user-check',
    shortcut: undefined,
    type: 'value_none',
    description: 'Skip the ownership check for the Moodle data directory. Useful when running moosh as a different user than the web server.',
  },
  {
    name: '--performance',
    shortcut: '-t',
    type: 'value_none',
    description: 'Show performance and timing information after command execution.',
  },
  {
    name: '--output',
    shortcut: '-o',
    type: 'value_required',
    description: 'Output format for tabular data. Supported formats: table, csv, json, oneline.',
    default: 'table',
  },
  {
    name: '--run',
    shortcut: undefined,
    type: 'value_none',
    description: 'Execute in write mode. Commands that modify the database require this flag to actually perform changes (dry-run by default).',
  },
];
