
const DATA_ENDPOINT = process.env.NODE_ENV === 'development' ? 'http://pluriversidadnomade.local/wp-json/kd_graph/v1/tagdata' : 'https://pluriversidadnomade.net/wp-json/kd_graph/v1/tagdata';

export { DATA_ENDPOINT };