// This file is part of Stack - https://stack.maths.ed.ac.uk
//
// Stack is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Stack is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Stack.  If not, see <http://www.gnu.org/licenses/>.

import path from 'path';
import { fileURLToPath } from 'url';
import fs from 'fs';
import webpack from 'webpack';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);
const ASCII_STRING_PREFIX = 'asciistring';

function loadAsciiStrings() {
    const languageFile = path.resolve(__dirname, '../../lang/en/qtype_stack.php');
    const languageSource = fs.readFileSync(languageFile, 'utf8');
    const strings = {};
    const languageStringPattern = /\$string\['([^']+)'\]\s*=\s*'((?:\\.|[^\\'])*)';/g;
    let match;

    while ((match = languageStringPattern.exec(languageSource)) !== null) {
        const key = match[1];
        if (key.indexOf(ASCII_STRING_PREFIX) === 0) {
            strings[key] = decodePhpSingleQuotedString(match[2]);
        }
    }

    return strings;
}

function decodePhpSingleQuotedString(value) {
    return value.replace(/\\\\|\\'/g, (match) => match === "\\'" ? "'" : '\\');
}

export default (env, argv) => {
    const isProduction = argv && argv.mode === 'production';

    return {
        entry: {
            'stack-web': './src/StackAsciiDisplay.js'
        },
        output: {
            filename: '[name].bundle.js',
            path: path.resolve(__dirname, 'dist'),
            library: {
                type: 'umd',
                name: 'StackWeb'
            },
            clean: true,
            publicPath: ''
        },
        module: {
            rules: [
                {
                    test: /\.js$/,
                    exclude: /node_modules/,
                    use: {
                        loader: 'babel-loader',
                        options: {
                            presets: ['@babel/preset-env']
                        }
                    }
                },
                {
                    test: /\.css$/,
                    use: ['style-loader', 'css-loader']
                }
            ]
        },
        optimization: {
            minimize: isProduction,
            splitChunks: false
        },
        plugins: [
            new webpack.DefinePlugin({
                __STACK_ASCII_STRINGS__: JSON.stringify(loadAsciiStrings())
            })
        ],
        devtool: 'source-map'
    };
};
