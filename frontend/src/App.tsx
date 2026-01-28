import { useEffect, useState } from 'react';
import type { NewTask, Task } from './types';

const API_URL = 'http://127.0.0.1:8000/api/tasks';

function App() {
  const [tasks, setTasks] = useState<Task[]>([]);
  const [newTask, setNewTask] = useState<NewTask>({ title: '', description: '', priority: 1 });
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState('');

  const fetchTasks = async () => {
    try {
      const res = await fetch(API_URL);
      const data = await res.json();
      setTasks(data);
    } catch (err) {
      console.error('Error fetching tasks:', err);
    }
  };

  useEffect(() => {
    fetchTasks();
  }, []);

  const handleSubmit = async (e: React.SubmitEvent) => {
    e.preventDefault();

    if (!newTask.title) {
      return;
    }

    setLoading(true);
    setError('');

    try {
      const res = await fetch(API_URL, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(newTask),
      });

      if (!res.ok) {
        const errData = await res.json();

        
        if (errData.errors) {
            const errorMessages = Object.values(errData.errors).join(', ');

            console.log(errorMessages)
            throw new Error(errorMessages);
        }
        
        throw new Error(errData.error || 'Failed to save task.');
      }

      await fetchTasks();
      setNewTask({ title: '', description: '', priority: 1 });
    } catch (err: any) {
      setError(err.message);
    } finally {
      setLoading(false);
    }
  };

  const toggleStatus = async (task: Task) => {
    try {
      const res = await fetch(`${API_URL}/${task.id}`, {
        method: 'PATCH',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ status: !task.status }),
      });

      if (!res.ok) {
        throw new Error('Failed to update status');
      }

      const updatedTasks = tasks.map(t =>
        t.id === task.id ? { ...t, status: !t.status } : t
      );
      setTasks(updatedTasks);
    } catch (err) {
      console.error('Error updating task status:', err);
    }
  };

  const getPriorityColor = (p: number) => {
    if (p === 3) {
      return 'bg-red-100 text-red-800 border-red-200';
    }
    if (p === 2) {
      return 'bg-yellow-100 text-yellow-800 border-yellow-200';
    }
    return 'bg-green-100 text-green-800 border-green-200';
  };

  return (
    <div className="min-h-screen bg-gray-50 p-8 font-sans text-gray-800">
      <div className="max-w-3xl mx-auto">
        <header className="mb-8 text-center">
          <h1 className="text-4xl font-extrabold text-blue-600 mb-2">QuickTask</h1>
          <p className="text-gray-500">Manage tasks</p>
        </header>

        <div className="bg-white p-6 rounded-xl shadow-md mb-8 border border-gray-100">
          <h2 className="text-xl font-bold mb-4">Add a new task</h2>
          <form onSubmit={handleSubmit} className="space-y-4">
            <div>
              <input
                type="text"
                placeholder="What needs to be done?"
                className="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition"
                value={newTask.title}
                onChange={e => setNewTask({ ...newTask, title: e.target.value })}
                required
              />
            </div>
            <div className="flex gap-4">
              <input
                type="text"
                placeholder="Optional description..."
                className="flex-grow p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none"
                value={newTask.description}
                onChange={e => setNewTask({ ...newTask, description: e.target.value })}
              />
              <select
                className="p-3 border border-gray-300 rounded-lg bg-white"
                value={newTask.priority}
                onChange={e => setNewTask({ ...newTask, priority: Number(e.target.value) })}
              >
                <option value={1}>Low (1)</option>
                <option value={2}>Medium (2)</option>
                <option value={3}>High (3)</option>
              </select>
            </div>

            {error && <p className="text-red-500 text-sm">{error}</p>}

            <button
              disabled={loading}
              className="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg transition disabled:opacity-50"
            >
              {loading ? 'Adding...' : 'Add Task'}
            </button>
          </form>
        </div>

        <div className="space-y-3">
          {tasks.length === 0 && <p className="text-center text-gray-400">No tasks. Add new.</p>}

          {tasks.map(task => (
            <div
              key={task.id}
              onClick={() => toggleStatus(task)}
              className={`
                group flex items-center justify-between p-4 bg-white rounded-lg border shadow-sm cursor-pointer transition-all hover:shadow-md
                ${task.status ? 'opacity-60 bg-gray-50' : 'hover:border-blue-300'}
              `}
            >
              <div className="flex items-center gap-4">
                <div className={`
                  w-6 h-6 rounded-full border-2 flex items-center justify-center transition
                  ${task.status ? 'bg-green-500 border-green-500' : 'border-gray-300 group-hover:border-blue-400'}
                `}>
                  {task.status && <span className="text-white text-xs">✓</span>}
                </div>

                <div>
                  <h3 className={`font-semibold text-lg ${task.status ? 'line-through text-gray-500' : 'text-gray-800'}`}>
                    {task.title}
                  </h3>
                  {task.description && <p className="text-gray-400 text-sm">{task.description}</p>}
                </div>
              </div>

              <span className={`px-3 py-1 rounded-full text-xs font-bold border ${getPriorityColor(task.priority)}`}>
                Priority {task.priority}
              </span>
            </div>
          ))}
        </div>
      </div>
    </div>
  );
}

export default App;
