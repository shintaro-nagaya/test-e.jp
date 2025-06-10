#!/usr/bin/sh

~/.nodebrew/current/bin/node ~/.nodebrew/current/bin/yarn install
~/.nodebrew/current/bin/node ~/.nodebrew/current/bin/yarn build

# yarn local install の場合
# ~/.nodebrew/current/bin/node ~/node_modules/yarn/bin/yarn.js install
# ~/.nodebrew/current/bin/node ~/node_modules/yarn/bin/yarn.js build