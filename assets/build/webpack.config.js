var WebpackNotifierPlugin = require( 'webpack-notifier' );
var path = require( 'path' );

module.exports = {
  module : {
    loaders: [ { 
        test   :  /\.(js|jsx)$/,
        loader : 'babel-loader',
        query: {
          presets: [
            'babel-preset-es2015-native-modules',
            'babel-preset-react'
          ].map( require.resolve )
        } 
      }
    ]
  },
  resolve: {
    modules: ['../', 'node_modules']
  },
  plugins: [
    new WebpackNotifierPlugin(),
  ]
};