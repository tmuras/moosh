export interface CommandOption {
  name: string;
  shortcut?: string;
  type: 'value_required' | 'value_optional' | 'value_none';
  description: string;
  default?: string;
}

export interface CommandArgument {
  name: string;
  required: boolean;
  isArray: boolean;
  description: string;
}

export interface Command {
  name: string;
  category: string;
  description: string;
  help?: string;
  bootstrapLevel: string;
  arguments: CommandArgument[];
  options: CommandOption[];
  examples?: string[];
  sinceVersion?: string;
}

export interface Category {
  slug: string;
  label: string;
  description: string;
}
