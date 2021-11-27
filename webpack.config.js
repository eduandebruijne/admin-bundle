const CopyPlugin = require("copy-webpack-plugin");
const path = require('path');
const webpack = require('webpack');

module.exports = {
  entry: './src/Resources/assets/app.js',
  output: {
      filename: 'dist.js',
      path: path.resolve(__dirname, 'src/Resources/public'),
  },
  plugins: [
      new webpack.ProvidePlugin({
          $: 'jquery'
      }),
      new CopyPlugin({
        patterns: [
          {
            from: "node_modules/tinymce/skins/content/default/content.css",
            to: "skins/content/default/content.css"
          },
          {
            from: "node_modules/tinymce/skins/ui/oxide/skin.min.css",
            to: "skins/ui/oxide/skin.min.css"
          },
          {
            from: "node_modules/tinymce/skins/ui/oxide/content.min.css",
            to: "skins/ui/oxide/content.min.css"
          },
        ],
      }),
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