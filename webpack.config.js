const path = require('path');
const webpack = require('webpack');

module.exports = {
    entry: './src/Resources/assets/app.js',
    output: {
        filename: 'all.js',
        path: path.resolve(__dirname, 'src/Resources/public'),
    },
    plugins: [
        new webpack.ProvidePlugin({
            $: 'jquery'
        })
    ],
    module: {
        rules: [
          {
            test: /\.scss$/i,
            use: ["style-loader", "css-loader", 'sass-loader'],
          },
          {
            test: /\.css$/i,
            use: ["style-loader", "css-loader"],
          },
        ],
      },
};