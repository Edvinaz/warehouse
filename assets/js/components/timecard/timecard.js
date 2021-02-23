import React from 'react';
import axios from 'axios';

class timecard extends React.Component {
    constructor() {
        super();

        this.state = {
            days: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31],
            staff: [1, 2],
            workedHours: 8
        }
    }

    componentDidMount() {
        this.getTimeCard();
    }

    getTimeCard = async () => {
        console.log('getTimeCard');
        axios
            .get('./timeCardAPI')
            .then((data) => {
                console.log(data.data);
                this.setState({ staff: data.data })
                if (this.state.staff[0].worked.length == 28) {
                    this.setState({ days: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28] })
                }
                if (this.state.staff[0].worked.length == 29) {
                    this.setState({ days: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29] })
                }
                if (this.state.staff[0].worked.length == 30) {
                    this.setState({ days: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30] })
                }
                if (this.state.staff[0].worked.length == 31) {
                    this.setState({ days: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31] })
                }
            });
    }

    updateWorkedHours(hours) {
        this.setState({ workedHours: hours })
    }

    updateTime(hours, day, worker) {
        console.log('./timeCard/' + this.state.staff[worker].staffId + '/' + day + '/' + hours);
        axios
            .get('./timeCard/' + this.state.staff[worker].staffId + '/' + day + '/' + hours)
            .then((data) => {
                this.state.staff[worker].worked[day - 1] = data.data
                this.forceUpdate()
            })
    }

    render() {

        return <div className="card-body">
            <a className={this.state.workedHours == 3 ? "btn btn-sm btn-primary" : "btn btn-sm btn-outline-secondary"} type="button" onClick={() => (this.updateWorkedHours(3))}>3</a>
            <a className={this.state.workedHours == 4 ? "btn btn-sm btn-primary" : "btn btn-sm btn-outline-secondary"} type="button" onClick={() => (this.updateWorkedHours(4))}>4</a>
            <a className={this.state.workedHours == 5 ? "btn btn-sm btn-primary" : "btn btn-sm btn-outline-secondary"} type="button" onClick={() => (this.updateWorkedHours(5))}>5</a>
            <a className={this.state.workedHours == 6 ? "btn btn-sm btn-primary" : "btn btn-sm btn-outline-secondary"} type="button" onClick={() => (this.updateWorkedHours(6))}>6</a>
            <a className={this.state.workedHours == 7 ? "btn btn-sm btn-primary" : "btn btn-sm btn-outline-secondary"} type="button" onClick={() => (this.updateWorkedHours(7))}>7</a>
            <a className={this.state.workedHours == 8 ? "btn btn-sm btn-primary" : "btn btn-sm btn-outline-secondary"} type="button" onClick={() => (this.updateWorkedHours(8))}>8</a>
            <a className={this.state.workedHours == 9 ? "btn btn-sm btn-primary" : "btn btn-sm btn-outline-secondary"} type="button" onClick={() => (this.updateWorkedHours(9))}>9</a>
            <a className={this.state.workedHours == 10 ? "btn btn-sm btn-primary" : "btn btn-sm btn-outline-secondary"} type="button" onClick={() => (this.updateWorkedHours(10))}>10</a>
            <a className={this.state.workedHours == 11 ? "btn btn-sm btn-primary" : "btn btn-sm btn-outline-secondary"} type="button" onClick={() => (this.updateWorkedHours(11))}>11</a>
            <a className={this.state.workedHours == 12 ? "btn btn-sm btn-primary" : "btn btn-sm btn-outline-secondary"} type="button" onClick={() => (this.updateWorkedHours(12))}>12</a>
            <a className={this.state.workedHours == -8 ? "btn btn-sm btn-primary" : "btn btn-sm btn-outline-secondary"} type="button" onClick={() => (this.updateWorkedHours(-8))}>Atostogos</a>
            <a className={this.state.workedHours == -10 ? "btn btn-sm btn-primary" : "btn btn-sm btn-outline-secondary"} type="button" onClick={() => (this.updateWorkedHours(-10))}>Neatvyko</a>
            <a className={this.state.workedHours == -12 ? "btn btn-sm btn-primary" : "btn btn-sm btn-outline-secondary"} type="button" onClick={() => (this.updateWorkedHours(-12))}>Liga</a>

            {/* Atostogos -8
    Neatvyko -10
    Liga -12
*/}
            <table width="100%" border="1">
                <thead>
                    <tr>
                        <td width="15%" key="name" align="center"><b>Vardas, Pavardė</b></td>
                        {this.state.days.map((day) => (
                            <td width="2.74%" align="center" key={day}><b>{day}</b></td>
                        ))}
                    </tr>
                </thead>

                {this.state.staff.map((worker, index) => (
                    <tbody>
                        { worker.worked ?
                            <tr key={worker.id}>
                                <td key={worker.name} align="center" onClick={() => (console.log(worker.worked))}>{worker.name}</td>

                                {worker.worked.map((day, dindex) => (
                                    day.weekDay == 6 || day.weekDay == 7 ?
                                        <td key={day.date} className="time-card-day bg-warning" align="center" onClick={() => (this.updateTime(this.state.workedHours, day.dayString, index))}>{day.hours}
                                            <span key={day.date} className="time-card-info">
                                                {day.date}<br />
                                                {day.worked.objects ?
                                                    day.worked.objects.map((object) => (
                                                        <span key={object.id}><b>{object.id}.</b> {object.name} ({object.hours} val)<br /></span>
                                                    ))
                                                    :
                                                    <span></span>
                                                }
                                            </span>
                                        </td>
                                        :
                                        <td key={day.date} className="time-card-day" align="center" onClick={() => (this.updateTime(this.state.workedHours, day.dayString, index))}>
                                            {day.hours}
                                            <span key={day.date} className="time-card-info">
                                                {day.date}<br />
                                                {day.worked.objects ?
                                                    day.worked.objects.map((object) => (
                                                        <span key={object.id}><b>{object.id}.</b> {object.name} ({object.hours} val)<br /></span>
                                                    ))
                                                    :
                                                    <span></span>
                                                }
                                            </span>
                                        </td>
                                ))}
                            </tr>
                            :
                            <span>Loading...</span>
                        }
                    </tbody>
                ))}
            </table>
        </div>
    }
}

export default timecard;