import * as echarts from "echarts/core";
import { GraphChart } from "echarts/charts";
import { TooltipComponent, DatasetComponent, TransformComponent, TitleComponent } from "echarts/components";
import { CanvasRenderer } from "echarts/renderers";
import { DATA_ENDPOINT } from "./config";
import axios from "axios";

console.log(DATA_ENDPOINT);

echarts.use([GraphChart, TooltipComponent, DatasetComponent, TransformComponent, TitleComponent, CanvasRenderer]);

const buildChart = async () => {
    const { data } = await axios.get(DATA_ENDPOINT);
    console.log(data);

    const kdGraph = echarts.init(document.getElementById("kd-graph"));
    kdGraph.setOption({
        title: {},
        
        series: [
            {
                name: 'Etiquetas pluriversidad',
                type: 'graph',
                layout: 'force',
                data: data.items,
                links: data.links,
                itemStyle: {
                    color: "#ffffff",
                    borderColor: "#000000",
                },
                label: {
                    show: true,
                    position: "bottom"
                },
                force: {
                    repulsion: 100,
                    edgeLength: 100
                }
            }
        ],
        media: [
            {
                query: {
                    maxWidth: 768,
                },
                option: {
                    series: [
                        {
                            label: {
                                show: true
                            }
                        }
                    ]
                }
            }
        ],
    });

    kdGraph.on("click", (params) => {
        console.log(params.data.link);
        window.location = params.data.link;
    });


    //return data;
}

buildChart();



