<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Employee;
use App\Models\ProjectAssignment;
use App\Models\AuditLog;
use Carbon\Carbon;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $query = Project::with(['employees']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('project_id', 'like', "%{$search}%")
                  ->orWhere('client', 'like', "%{$search}%")
                  ->orWhere('manager', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $projects = $query->withCount('employees')->orderBy('updated_at', 'desc')->paginate(10)->withQueryString();
        $allEmployees = Employee::active()->orderBy('full_name')->get();

        return view('admin.projects.index', compact('projects', 'allEmployees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'project_id' => 'required|string|unique:projects,project_id',
            'name' => 'required|string|max:255',
            'client' => 'required|string',
            'manager' => 'required|string',
            'status' => 'required|in:ACTIVE,COMPLETED,ON_HOLD,ARCHIVED',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'description' => 'nullable|string',
        ]);

        $project = Project::create([
            'project_id' => trim($request->input('project_id')),
            'name' => trim($request->input('name')),
            'client' => trim($request->input('client')),
            'manager' => trim($request->input('manager')),
            'status' => $request->input('status', 'ACTIVE'),
            'start_date' => $request->input('start_date') ? Carbon::parse($request->input('start_date'))->format('Y-m-d') : null,
            'end_date' => $request->input('end_date') ? Carbon::parse($request->input('end_date'))->format('Y-m-d') : null,
            'description' => $request->input('description'),
            'is_demo' => false,
        ]);

        AuditLog::log('CREATE', 'PROJECT', (string) $project->id, [
            'name' => $project->name,
            'code' => $project->project_id,
        ]);

        return redirect()->route('admin.projects.index')->with('success', "Project '{$project->name}' created successfully.");
    }

    public function update(Request $request, $id)
    {
        $project = Project::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'client' => 'required|string',
            'manager' => 'required|string',
            'status' => 'required|in:ACTIVE,COMPLETED,ON_HOLD,ARCHIVED',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'description' => 'nullable|string',
        ]);

        $project->update([
            'name' => trim($request->input('name')),
            'client' => trim($request->input('client')),
            'manager' => trim($request->input('manager')),
            'status' => $request->input('status'),
            'start_date' => $request->input('start_date') ? Carbon::parse($request->input('start_date'))->format('Y-m-d') : null,
            'end_date' => $request->input('end_date') ? Carbon::parse($request->input('end_date'))->format('Y-m-d') : null,
            'description' => $request->input('description'),
        ]);

        AuditLog::log('UPDATE', 'PROJECT', (string) $project->id, [
            'name' => $project->name,
            'status' => $project->status,
        ]);

        return redirect()->back()->with('success', "Project '{$project->name}' updated.");
    }

    public function assignEmployee(Request $request, $id)
    {
        $project = Project::findOrFail($id);
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'role' => 'nullable|string',
        ]);

        ProjectAssignment::updateOrCreate(
            ['project_id' => $project->id, 'employee_id' => $request->input('employee_id')],
            ['role' => $request->input('role', 'Member')]
        );

        AuditLog::log('ASSIGN_EMPLOYEE', 'PROJECT', (string) $project->id, [
            'project' => $project->name,
            'employee_id' => $request->input('employee_id'),
            'role' => $request->input('role', 'Member'),
        ]);

        return redirect()->back()->with('success', "Employee assigned to project.");
    }

    public function removeEmployee($projectId, $employeeId)
    {
        ProjectAssignment::where('project_id', $projectId)->where('employee_id', $employeeId)->delete();

        AuditLog::log('REMOVE_EMPLOYEE', 'PROJECT', (string) $projectId, [
            'employee_id' => $employeeId,
        ]);

        return redirect()->back()->with('success', "Employee unassigned from project.");
    }

    public function destroy($id)
    {
        $project = Project::findOrFail($id);
        $name = $project->name;
        $project->delete();

        AuditLog::log('DELETE', 'PROJECT', (string) $id, ['name' => $name]);

        return redirect()->route('admin.projects.index')->with('success', "Project '{$name}' deleted.");
    }
}
