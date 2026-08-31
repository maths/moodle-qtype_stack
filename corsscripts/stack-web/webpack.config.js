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

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

export default {
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
    resolve: {
        extensions: ['.js'],
        alias: {
            '@ascii': path.resolve(__dirname, '../ascii'),
            '@filters': path.resolve(__dirname, '../ascii/filters'),
            '@extractors': path.resolve(__dirname, '../ascii/extractors')
        }
    },
    externals: {
        // MathJax is loaded separately by Moodle/HTML
        'mathjax': 'MathJax',
        // markdown-it is bundled in stackascii.js
        'markdown-it': 'markdownit'
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
        minimize: true,
        splitChunks: false
    },
    devtool: 'source-map'
};
