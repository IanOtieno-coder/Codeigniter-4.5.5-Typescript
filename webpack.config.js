const path = require("path");

module.exports = {
  entry: {
    app: "./public/assets/src/globals/index.ts",
    admin_dsahboard: './public/assets/src/admin/Dsahboard/index.ts', 
},
  output: {
    filename: "[name].bundle.js",
    path: path.resolve(__dirname, "public/assets/js/dist"),
  },
  resolve: {
    extensions: [".ts", ".js"],
  },
  module: {
    rules: [
      {
        test: /\.ts$/,
        use: "ts-loader",
        exclude: /node_modules/,
      },

      {
        test: /\.css$/, // Process CSS files
        use: ["style-loader", "css-loader"],
      },
    ],
  },
  mode: "development",
};
